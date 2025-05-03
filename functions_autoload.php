<?php

if (! function_exists('pkcs12Read')) {
    /**
     * Function that analyzes an array of pkcs12 certificates
     * @param string $certificate
     * @param array $certInfo
     * @param string $password
     * @return array|string[]
     */
    function pkcs12Read(string $certificate, array &$certInfo, string $password): bool
    {
        if (openssl_pkcs12_read($certificate, $certInfo, $password)) {
            return true;
        }
        $msg = openssl_error_string();
        if ($msg === 'error:0308010C:digital envelope routines::unsupported') {
            if (!shell_exec('openssl version')) {
                return false;
            }
            $tempPassword = tempnam(sys_get_temp_dir(), 'pfx');
            $tempEncriptedOriginal = tempnam(sys_get_temp_dir(), 'original');
            $tempEncriptedRepacked = tempnam(sys_get_temp_dir(), 'repacked');
            $tempDecrypted = tempnam(sys_get_temp_dir(), 'decripted');
            file_put_contents($tempPassword, $password);
            file_put_contents($tempEncriptedOriginal, $certificate);
            shell_exec(<<<REPACK_COMMAND
                cat $tempPassword | openssl pkcs12 -legacy -in $tempEncriptedOriginal -nodes -out $tempDecrypted -passin stdin &&
                cat $tempPassword | openssl pkcs12 -in $tempDecrypted -export -out $tempEncriptedRepacked -passout stdin
                REPACK_COMMAND
            );
            $certificateRepacked = file_get_contents($tempEncriptedRepacked);
            unlink($tempPassword);
            unlink($tempEncriptedOriginal);
            unlink($tempEncriptedRepacked);
            unlink($tempDecrypted);
            openssl_pkcs12_read($certificateRepacked, $certInfo, $password);
            return true;
        }
        return false;
    }
}
