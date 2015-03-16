<?php
/**
 * Created by PhpStorm.
 * User: vir-mir
 * Date: 13.03.15
 * Time: 19:16
 */

namespace TraversalRouterPHP;

use TraversalRouterPHP\Exception\Traversal as ExceptionTraversal;

abstract class Traversal {

    private $namespace;
    private $namespaceMain;
    private $dir;

    /**
     * @var \TraversalRouterPHP\Action
     */
    private $action;


    /**
     * @var $this;
     */
    private $parent;



    public function __construct($dir, $parent = null) {
        $namespace = explode('\\', get_class($this));
        array_pop($namespace);
        $this->namespace = implode('\\', $namespace);
        array_pop($namespace);
        $this->namespaceMain = implode('\\', $namespace);
        $this->parent = $parent;
        $this->dir = $dir;
    }


    /**
     * @param string[] $urls
     * @return $this
     * @throws ExceptionTraversal
     */
    public function match($urls) {

        $urls = $this->getObjectParam(is_array($urls) ? $urls : [$urls], $object, $param);
        $this->logic($object, $param);

        if (!$urls) {
            return $object;
        }

        return $object->match($urls);
    }

    /**
     * @return \TraversalRouterPHP\Action
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @return null|Traversal
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @param Traversal $object
     * @param string[] $param
     * @throws ExceptionTraversal
     */
    protected function logic($object, $param) {
        $objectLogic = $object->namespace. '\\Logic';

        if (file_exists($this->dir . '/' . str_replace('\\', '/', $objectLogic) . '.php')) {
            $object->action = new $objectLogic($param);
        } else {
            throw new ExceptionTraversal('undefined action for the traversal "' . get_class($object) . '".');
        }
    }

    protected function getObjectParam($urls, &$object, &$param) {
        $urls = $urls ? $urls : [];
        $param = [];
        while ($urls) {
            $url = array_shift($urls);
            $objectName = $this->getObjectName($url);
            if ($objectName) {
                $object = new $objectName($this->dir, $this);
                break;
            } else {
                array_push($param, $url);
            }
        }

        if (!$object) $object = $this;

        return $urls;
    }

    protected function getObjectName($url) {
        $objectNames = [$url, substr($url, 0, -1), substr($url, 0, -2)];
        foreach ($objectNames as $url) {
            $objectName = $this->namespaceMain . '\\' . ucfirst($url) . '\\Route';
            $file = $this->dir . '/' . str_replace('\\', '/', $objectName) . '.php';
            if (file_exists($file)) return $objectName;
        }
        return false;
    }

} 