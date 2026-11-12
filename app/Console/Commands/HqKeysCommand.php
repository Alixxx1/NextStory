<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HqKeysCommand extends Command
{
    protected $signature = 'hq:keys';
    protected $description = 'توليد زوج مفاتيح RSA لتوقيع التراخيص (الخاص للمالك فقط، والعام يوضع في config/license.php)';

    public function handle(): int
    {
        $k = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($k, $priv);
        $this->line('HQ_PRIVATE_KEY=' . base64_encode($priv));
        $this->line('LICENSE_PUBLIC_KEY=' . base64_encode(openssl_pkey_get_details($k)['key']));
        return 0;
    }
}
