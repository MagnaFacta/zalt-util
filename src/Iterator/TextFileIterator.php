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
class TextFileIterator implements \Countable, \Iterator
{
    /**
     * @var ?int
     */
    protected ?int $_count = null;

    protected readonly string $_encoding;

    /**
     *
     * @var array
     */
    protected array $_fieldMap;

    /**
     * Count of the fieldmap
     *
     * @var int
     */
    protected int $_fieldMapCount;

    /**
     *
     * @var bool|null|\SplFileObject
     */
    protected bool|null|\SplFileObject $_file = null;

    /**
     * /**
     * The position of the current item in the file
     *
     * @var ?int
     */
    protected ?int $_filepos = null;

    /**
     * The current key value
     *
     * @var int
     */
    protected int $_key = 0;

    /**
     *
     * @var boolean
     */
    protected bool $_valid = true;

    public function __construct(
        protected readonly string $filename,
        protected readonly string $split = "",
        ?string                   $encoding = null)
    {
        if ($encoding && ($encoding != mb_internal_encoding())) {
            $this->_encoding = $encoding;
        } else {
            $this->_encoding = '';
        }
    }

    /**
     * Return the string representation of the object.
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'encoding' => $this->_encoding,
            'filename' => $this->filename,
            'filepos' => $this->_filepos,
            'key' => $this->_key - 1,
            'split' => $this->split,
        ];
    }

    /**
     * Called during unserialization of the object.
     *
     * @param array $data
     */
    public function __unserialize(array $data): void
    {
        if (!$data) {
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
        $this->_encoding = $data['encoding'];
        $this->_file = null;
        $this->filename = $data['filename'];
        $this->_filepos = $data['filepos'];
        $this->_key = $data['key'];
        $this->split = $data['split'];
    }

    /**
     *
     * @return boolean
     */
    private function _accept()
    {
        return (boolean)trim($this->_file->current(), "\r\n");
    }

    /**
     * Open the file and optionally restore the position
     *
     * @return void
     */
    private function _openFile()
    {
        $this->_fieldMap = [];
        $this->_fieldMapCount = 0;

        if (!file_exists($this->filename)) {
            $this->_file = false;
            return;
        }

        try {
            $this->_file = new \SplFileObject($this->filename, 'r');
            $firstline = trim(File::removeBOM($this->_file->current()));

            if ($firstline) {
                $this->_fieldMap = $this->_split($firstline);
                $this->_fieldMapCount = count($this->_fieldMap);

                // Check for fields, do not run when empty
                if (0 === $this->_fieldMapCount) {
                    $this->_file = false;
                    return;
                }
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

    public function _split($line)
    {
        if ($this->_encoding) {
            $line = mb_convert_encoding($line, mb_internal_encoding(), $this->_encoding);
        }

        if ($this->split) {
            return explode($this->split, $line);
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
     * @return mixed
     */
    public function current(): mixed
    {
        if (null === $this->_file) {
            $this->_openFile();
        }

        if (! ($this->_file instanceof \SplFileObject && $this->_valid)) {
            return false;
        }

        $fields     = $this->_split(trim($this->_file->current(), "\r\n"));
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