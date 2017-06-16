<?php
namespace Datenarong\HealthCheck\Classes;

class Oracle extends Base
{
    private $conn;

    public function __construct()
    {
        parent::__construct();
        
        $this->outputs['module'] = 'Oracle';
        $this->conf = ['host', 'port', 'username', 'password', 'dbname', 'charset'];
    }

    public function connect($conf)
    {
        $this->outputs['service'] = 'Check Connection';

        // Validate parameter
        if (false === $this->validParams($conf)) {
            $this->outputs = [
                'status' => 'ERROR',
                'remark' => 'Require parameter (' . implode(',', $this->conf) . ')'
            ];
        }

        // Set url
        $this->outputs['url'] = $conf['host'];

        try {
            // Connect to oracle
            $this->conn = oci_connect($conf['username'], $conf['password'], "{$conf['host']}:{$conf['port']}/{$conf['db']}", $conf['charset']);
        
            if (!$this->conn) {
                $this->outputs = [
                    'status'  => 'ERROR',
                    'remark'  => 'Can\'t connect to database'
                ];
            }
        } catch (Exception $e) {
            $this->outputs = [
                'status'  => 'ERROR',
                'remark'  => 'Can\'t connect to database : ' . $e->getMessage()
            ];
        }

        return $this->outputs;
    }

    public function query($conf, $sql)
    {
        $this->outputs['service'] = 'Check Query Datas';

        // Validate parameter
        if (false === $this->validParams($conf)) {
            $this->outputs = [
                'status' => 'ERROR',
                'remark' => 'Require parameter (' . implode(',', $this->conf) . ')'
            ];
        }

        // Set url
        $this->outputs['url'] = $conf['host'];

        try {
            // Connect to oracle
            $this->conn = oci_connect($conf['username'], $conf['password'], "{$conf['host']}:{$conf['port']}/{$conf['db']}", $conf['charset']);
        
            if (!$this->conn) {
                $this->outputs = [
                    'status'  => 'ERROR',
                    'remark'  => 'Can\'t connect to database'
                ];
            }
        } catch (Exception $e) {
            $this->outputs = [
                'status'  => 'ERROR',
                'remark'  => 'Can\'t connect to database : ' . $e->getMessage()
            ];
        }

        // Query
        try {
            $orc_parse = oci_parse($this->conn, $sql);
            $orc_exec = oci_execute($orc_parse);
            oci_free_statement($orc_parse);

            if (!$orc_exec) {
                $this->outputs = [
                    'status'  => 'ERROR',
                    'remark'  => 'Can\'t query datas'
                ];
            }
        } catch (\Exception $e) {
            $this->outputs = [
                'status'  => 'ERROR',
                'remark'  => 'Can\'t query datas : ' . $e->getMessage()
            ];
        }

        return $this->outputs;
    }
    
    public function __destruct()
    {
        parent::__destruct();

        // Disconnect database
        $this->conn = null;
    }
}
