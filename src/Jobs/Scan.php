<?php

namespace Ferdiunal\NovaTranslations\Jobs;

use Ferdiunal\NovaTranslations\Services\SaveScan;
use Ferdiunal\NovaTranslations\Translators\Translator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class Scan implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?Translator $translator = null
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $scanner = new SaveScan;
        if ($this->translator instanceof Translator) {
            $scanner->setTranslator($this->translator);
        }
        $scanner->handle();
    }
}
