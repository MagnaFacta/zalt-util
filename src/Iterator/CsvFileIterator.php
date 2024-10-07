<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Iterator
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Iterator;

use Zalt\File\File;

/**
 * @package    Zalt
 * @subpackage Iterator
 * @since      Class available since version 1.0
 */
class CsvFileIterator implements \Countable, \Iterator
{
    protected $delimiter;
    protected $enclosure;
    protected $escape;
    protected $filename;

    protected $_autoSenseDelim = true;

    /**
     * @var int
     */
    protected $_count = null;

    protected $_encoding;

    /**
     *
     * @var array
     */
    protected array $_fieldMap = [];

    /**
     * Count of the fieldmap
     *
     * @var int
     */
    protected $_fieldMapCount = 0;

    /**
     *
     * @var \SplFileObject|null|bool
     */
    protected \SplFileObject|null|bool $_file = null;

    /**
     * The position of the current item in the file
     *
     * @var int|null
     */
    protected ?int $_filepos = null;

    /**
     * The current key value
     *
     * @var mixed
     */
    protected $_key = 0;

    /**
     * The function that splits the input string into an array
     *
     * @var callable
     */
    protected $_splitFunction;

    /**
     *
     * @var boolean
     */
    protected $_valid = true;

    public function __construct($filename, $encoding = null, $delimiter = ",", $enclosure = '"', $escape = "\\")
    {
        $this->_encoding = $encoding;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape    = $escape;

        $this->filename  = $filename;
    }

    /**
     * Return the string representation of the object.
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'filename' => $this->filename,
            'filepos'  => $this->_filepos,
            'key'      => $this->_key - 1,
        ];
    }

    /**
     * Called during unserialization of the object.
     *
     * @param array $data
     */
    public function __unserialize(array $data): void
    {
        if (! $data) {
            $lastErr = error_get_last();
            error_log($lastErr['message'] . "\n", 3, ini_get('error_log'));
            return;
        }

        // WARNING! WARNING! WARNING!
        //
        // Do not reopen the file in the unserialize statement
        // 1 - the file gets locked
        // 2 - if the file is deleted you cannot reopen your session.
        //
        // Normally this is not a problem, but when
        $this->_file          = null;
        $this->filename       = $data['filename'];
        $this->_filepos       = $data['filepos'];
        $this->_key           = $data['key'];
    }

    /**
     *
     * @return boolean
     */
    private function _accept()
    {
        return (boolean) trim($this->_file->current(), "\r\n");
    }

    /**
     * Open the file and optionally restore the position
     *
     * @return void
     */
    private function _openFile()
    {
        $this->_fieldMap      = [];
        $this->_fieldMapCount = 0;

        if (! file_exists($this->filename)) {
            $this->_file = false;
            return;
        }

        try {
            $this->_file = new \SplFileObject($this->filename, 'r');
            $firstline   = File::removeBOM($this->_file->current());

            if ($firstline) {
                if ($this->_autoSenseDelim) {
                    $colon     = str_getcsv($firstline, ',', $this->enclosure, $this->escape);
                    $semicolon = str_getcsv($firstline, ';', $this->enclosure, $this->escape);
                    $this->delimiter = (count($semicolon) > count($colon)) ? ';' : ',';
                }
                $this->_fieldMap      = str_getcsv($firstline, $this->delimiter, $this->enclosure, $this->escape);
                $this->_fieldMapCount = count($this->_fieldMap);
            }

            // Check for fields, do not run when empty
            if (0 == $this->_fieldMapCount) {
                $this->_file = false;
                return;
            }

            // Restore old file position if any
            if (null !== $this->_filepos) {
                $this->_file->fseek($this->_filepos, SEEK_SET);
            }

            // Always move to next, even if there was no first line
            $this->next();

        } catch (\Exception $e) {
            $this->_file = false;
        }
    }

    /**
     * Transform the input into an array and recode the input to the correct encoding
     * (if any, the encoding is only set when different from the internal encoding)
     *
     * @param mixed $line String or array depending on file flags
     * @return array
     */
    protected function _recode($line)
    {
        // File flags means this should be an array
        if (! is_array($line)) {
            return array();
        }

        if ($this->_encoding) {
            foreach($line as &$field) {
                $field = str_replace($this->escape, '', mb_convert_encoding(trim($field), mb_internal_encoding(), $this->_encoding));
            }
        } else {
            foreach($line as &$field) {
                $field = str_replace($this->escape, '', trim($field));
            }
        }

        return $line;
    }

    /**
     * Return the number of records in the file
     *
     * @return int
     */
    public function count(): int
    {
        if ($this->_count === null) {
            // Save position like in serialize
            $key = $this->key() - 1;
            $filepos = $this->_filepos;

            $this->rewind();
            $this->_count = 0;
            foreach($this as $row)
            {
                $this->_count++;
            }

            // Now restore position
            $this->_key = $key;
            $this->_filepos = $filepos;
            $this->_openFile();
        }

        return $this->_count;
    }

    /**
     * Return the current element
     *
     * @return array|false
     */
    public function current(): mixed
    {
        if (null === $this->_file) {
            $this->_openFile();
        }

        if (! ($this->_file instanceof \SplFileObject && $this->_valid)) {
            return false;
        }

        $fields     = $this->_recode(str_getcsv($this->_file->current(), $this->delimiter, $this->enclosure, $this->escape));
        $fieldCount = count($fields);

        if (0 ===  $fieldCount) {
            return false;
        }

        if ($fieldCount > $this->_fieldMapCount) {
            // Remove extra fields from the input
            $fields = array_slice($fields, 0, $this->_fieldMapCount);

        } elseif ($fieldCount < $this->_fieldMapCount) {
            // Add extra nulls to the input
            $fields = $fields + array_fill($fieldCount, $this->_fieldMapCount - $fieldCount, null);
        }

        return array_combine($this->_fieldMap, $fields);
    }

    /**
     * Get the map array key value => field name to use
     *
     * This line can then be used to determined the mapping used by the mapping function.
     *
     * @return array
     */
    public function getFieldMap(): array
    {
        if (null === $this->_file) {
            $this->_openFile();
        }

        return $this->_fieldMap;
    }

    /**
     * Return the key of the current element
     *
     * @return int
     */
    public function key(): mixed
    {
        if (null === $this->_file) {
            $this->_openFile();
        }

        return $this->_key;
    }

    /**
     * Move forward to next element
     */
    public function next(): void
    {
        if (null === $this->_file) {
            $this->_openFile();
        }

        if ($this->_file) {
            $this->_key = $this->_key + 1;
            while ($this->_file->valid()) {
                $this->_file->next();
                $this->_filepos = $this->_file->ftell();
                if ($this->_accept()) {
                    $this->_valid = true;
                    return;
                }
            }
        }
        $this->_valid = false;
    }

    /**
     *  Rewind the \Iterator to the first element
     */
    public function rewind(): void
    {
        $this->_filepos = null;
        $this->_key = 0;

        if (null === $this->_file) {
            $this->_openFile();
        } elseif ($this->_file) {
            $this->_file->rewind();
            $this->_file->current(); // Reading line is nexessary for correct loading of file.
            $this->next();
        }
    }

    /**
     * Switch autosense for delimiter on/off
     *
     * Auto choose between colon and semicolon as delimiter
     *
     * @param bool $enabled
     */
    public function setAutoSenseDelimiter(bool $enabled): void
    {
        $this->_autoSenseDelim = $enabled;
    }

    /**
     * True if not EOF
     *
     * @return boolean
     */
    public function valid(): bool
    {
        if (null === $this->_file) {
            $this->_openFile();
        }

        return $this->_file && $this->_valid;
    }
}
