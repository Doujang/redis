<?php

namespace EasySwoole\Redis\CommandHandel;

use EasySwoole\Redis\CommandConst;
use EasySwoole\Redis\Redis;
use EasySwoole\Redis\Response;

class ZRevRangeByScore extends AbstractCommandHandel
{
    public $commandName = 'ZRevRangeByScore';
    protected $withScores = false;


    public function handelCommandData(...$data)
    {
        $key = array_shift($data);
        $max = array_shift($data);
        $min = array_shift($data);
        $withScores = array_shift($data);
        $this->withScores = $withScores;

        $command = [CommandConst::ZREVRANGEBYSCORE, $key, $max, $min];
        if ($withScores == true) {
            $command[] = 'WITHSCORES';
        }
        $commandData = array_merge($command);
        return $commandData;
    }


    public function handelRecv(Response $recv)
    {
        $data = $recv->getData();
        if ($this->withScores == true) {
            $result = [];
            foreach ($data as $k => $va) {
                if ($k % 2 == 0) {
                    $result[$this->unSerialize($va)] = 0;
                } else {
                    $result[$this->unSerialize($data[$k - 1])] = $va;
                }
            }
        } else {
            $result = [];
            foreach ($data as $k => $va) {
                $result[$k] = $this->unSerialize($va);
            }
        }
        return $result;
    }
}