<?php

namespace Evaneos\Hector\Events;

final class PublisherEvents
{
    const PRE_PUBLISH     = 'hector.pre_publish';
    const SUCCESS_PUBLISH = 'hector.success_publish';
    const FAIL_PUBLISH    = 'hector.failed_publish';
}
