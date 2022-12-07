<?php

namespace Zalt\Message;

enum MessageStatus: string
{
    case Danger = 'danger';
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
}