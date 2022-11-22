<?php

namespace Zalt\Message;

enum MessageStatus
{
    case Danger;
    case Error;
    case Info;
    case Success;
    case Warning;
}