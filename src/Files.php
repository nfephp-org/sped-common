<?php

namespace NFePHP\Common;

/**
 * Controla operações com arquivos
 *
 * Esta class mimetisa FRACAMENTE algumas funções do Flysystem
 * o pacote do Flysystem foi removido devido a conflitos de versão
 * com outros pacotes e aplicativos que usam a versão 2 do Flysystem
 */
class Files
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Construto
     * @param string $folder
     * @throws \Exception
     */
    public function __construct(string $folder = '')
    {
        if (!empty($folder)) {
            if (!is_dir($folder)) {
                if (!mkdir($folder, 0777, true) && !is_dir($folder)) {
                    throw new \Exception(
                        "Falhou ao tentar criar o path {$folder} (verifique as permissões de escrita)."
                    );
                }
            }
        }
        $this->path = $folder;
    }

    /**
     * Grava o arquivo e cria os diretorios inclusos no filename
     *
     * @param string $filename
     * @param string $content
     * @return boolean
     * @throws \Exception
     */
    public function put(string $filename, string $content)
    {
        $par = explode("/", $filename);
        if (count($par) > 1) {
            //tem mais diretorios
            $dir = '';
            for ($x = 0; $x < (count($par) - 1); $x++) {
                $dir .= $par[$x] . "/";
            }
            if (!is_dir($this->path . $dir)) {
                $path = $this->path . $dir;
                if (!mkdir($path, 0777, true)) {
                    throw new \Exception("Falhou ao tentar criar o path {$path} (verifique as permissões de escrita).");
                }
            }
        }
        if (file_put_contents($this->path . $filename, $content) === false) {
            throw new \Exception("Falhou ao tentar salvar o arquivo {$filename} (verifique as permissões de escrita).");
        }
        return true;
    }

    /**
     * Remove o arquivo, se exisitr
     * @param string $filename
     * @return boolean
     * @throws \Exception
     */
    public function delete(string $filename)
    {
        if (file_exists($filename)) {
            if (unlink($filename) === false) {
                throw new \Exception("Falhou ao tentar deletar o arquivo {$filename}.");
            }
        } elseif (is_file($this->path . DIRECTORY_SEPARATOR . $filename)) {
            if (unlink($this->path . DIRECTORY_SEPARATOR . $filename) === false) {
                throw new \Exception("Falhou ao tentar deletar o arquivo {$filename}.");
            }
        }
        return true;
    }

    /**
     * Lista o conteúdo da pasta indicada
     * @param string $folder
     * @return array
     */
    public function listContents(string $folder = '')
    {
        $new = [];
        if (is_dir($this->path . DIRECTORY_SEPARATOR . $folder)) {
            if (empty($folder)) {
                $list = glob($this->path . "*.*");
            } else {
                $list = glob($this->path . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . "*.*");
            }
            foreach ($list as $f) {
                $new[] = [
                    'type' => 'file',
                    'path' => $f
                ];
            }
        }
        return $new;
    }

    /**
     * Obtêm o timestamp da última alteração do arquivo indicado
     * @param string $path
     * @return int
     */
    public function getTimestamp(string $path)
    {
        if (file_exists($path)) {
            return filemtime($path);
        }
        return 0;
    }

    /**
     * Verifica que o arquivo ou pasta existe
     * @param string $path
     * @return bool
     */
    public function has(string $path)
    {
        if (is_dir($path)) {
            return true;
        } elseif (is_file($path)) {
            return true;
        } elseif (is_file($this->path . DIRECTORY_SEPARATOR . $path)) {
            return true;
        }
        return false;
    }
}
