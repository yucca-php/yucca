<?php
namespace Yucca\Component\ConnectionFactory;

interface ConnectionFactoryInterface {
    function getConnection(array $params);
}