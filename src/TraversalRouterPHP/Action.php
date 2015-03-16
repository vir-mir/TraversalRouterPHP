<?php
/**
 * Created by PhpStorm.
 * User: vir-mir
 * Date: 13.03.15
 * Time: 19:14
 */

namespace TraversalRouterPHP;

abstract class Action
{

    private $status;

    private $data;

    private $outputDataFormat;

    protected $headers = [
        'Cache-Control: no-cache, must-revalidate',
        'Pragma: no-cache',
    ];

    protected $methods = ['get'];


    public function __construct($data)
    {
        $this->data = $data;

        $this
            ->setDataFormat('html')
            ->setStatus();
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function isMethod($method) {
        return in_array(strtolower($method), array_map('strtolower', $this->methods));
    }

    /**
     * @return string
     */
    protected function getDataFormat()
    {
        return $this->outputDataFormat;
    }

    /**
     * @param int $status
     * @return $this
     */
    protected function setStatus($status = 200)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $header
     * @return $this
     */
    protected function setHeader($header)
    {
        array_push($this->headers, $header);
        return $this;
    }

    /**
     * @param string $dataFormat
     * @return $this
     */
    protected function setDataFormat($dataFormat)
    {
        switch ($dataFormat) {
            case 'png':
                $dataFormat = 'image/png';
                $format = 'image';
                break;
            case 'gif':
                $dataFormat = 'image/gif';
                $format = 'image';
                break;
            case 'jpg':
            case 'jpeg':
                $dataFormat = 'image/jpeg';
                $format = 'image';
                break;
            case 'pdf':
                $dataFormat = 'application/pdf';
                $format = 'pdf';
                break;
            case 'json':
                $dataFormat = 'application/json';
                $format = 'json';
                break;
            case 'text':
                $dataFormat = 'text/plain';
                $format = 'text';
                break;
            case 'xml':
                $dataFormat = 'text/xml';
                $format = 'xml';
                break;
            case 'html':
            default:
                $dataFormat = 'text/html';
                $format = 'html';
                break;
        }
        $this->outputDataFormat = $format;
        return $this->setHeader("Content-Type: {$dataFormat}; charset=utf-8");
    }
} 