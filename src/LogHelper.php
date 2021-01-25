<?php

declare(strict_types=1);

namespace Cplugins\Hlog;

use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;

/**
 * 日志类
 * 1.INFO：程序主入口用于参数记录以查看入参是否符合预期、计算和操作结果以检测结果是否符合预期。
 * 2.DEBUG：这个级别更多用于辅助开发人员定位问题点。在程序开发时建议使用此级别总览整个过程。
 * 3.WARN：在某个非常规操作处建议使用此，比如一个条件分支800没有一次，突然有了，这个时候要引起注意。
*  4.ERROR：系统已经出现问题，程序不能正常进行下去的时候使用此级别。比如：删除一个已经有业务关联（已经被锁定）的客户时。
 */
class LogHelper
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    // 获取调用者信息
    protected $caller;

    public function __construct()
    {
        // 第一个参数对应日志的 name, 第二个参数对应 config/autoload/logger.php 内的 key
        $this->logger = ApplicationContext::getContainer()->get(LoggerFactory::class)->get("");
        // 获取调用函数信息
        $this->caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,3);
    }

    /**
     * INFO：程序主入口用于参数记录以查看入参是否符合预期、计算和操作结果以检测结果是否符合预期。
     */
    public function info($content, $e = null)
    {
        $info = $this->mergeMessage($content, $e);
        return $this->logger->info($info, []);
    }

    /**
     * DEBUG：这个级别更多用于辅助开发人员定位问题点。在程序开发时建议使用此级别总览整个过程。
     */
    public function debug($content, $e = null) 
    {
        $info = $this->mergeMessage($content, $e);
        return $this->logger->debug($info, []);
    }

    /**
     * WARN：在某个非常规操作处建议使用此，比如一个条件分支800没有一次，突然有了，这个时候要引起注意。
     */
    public function warn($content, $e = null) 
    {
        $info = $this->mergeMessage($content, $e);
        return $this->logger->warning($info, []);
    }

    /**
     * ERROR：系统已经出现问题，程序不能正常进行下去的时候使用此级别。比如：删除一个已经有业务关联（已经被锁定）的客户时。
     */
    public function error($content, $e = null) 
    {
        $info = $this->mergeMessage($content, $e);
        return $this->logger->error($info);
    }

    /**
     * 合并message信息
     */
    private function mergeMessage($content, $e) 
    {
        $code = ""; $message = ""; $trace = "";
        if (!empty($e)) {
            $code = $e->getCode();
            $message = $e->getMessage();
            $trace = $e->getTraceAsString();
        } 
        
        // 获取调用者信息
        $callArr = array_filter($this->getCallPath($e));

        $msgArr = [$content, $code, $message, $trace];
        if (!empty($callArr)) {
            $msgArr = array_merge($callArr, $msgArr);
        }
        
        // $tempArr = array_filter($arr);
        // foreach ($arr as $item) {
        //     if (!empty($item)) {
        //         $tempArr[] = $item;
        //     }
        // }

        return implode(" ", $msgArr);
    }

    /**
     * 获取调用函数信息
     */
    private function getCallPath($e)
    {
        if (!empty($e)) {
            // 获取异常类的调用者
            $index = 0;
            $trace = $e->getTrace();
            $class = isset($trace[$index]["class"]) ? $trace[$index]["class"] : null;
            $function = isset($trace[$index]["function"]) ? $trace[$index]["function"] : null;
        } else {
            // 获取日志类的调用者
            $index = 1;
            // 调用类名
            $class  = isset($this->caller[$index]["class"]) ? $this->caller[$index]["class"] : null;
            // 调用方法名
            $function = isset($this->caller[$index]["function"]) ? $this->caller[$index]["function"] : null;
        }

        
        // 工程名称
        $projectName = BASE_PATH;

        if (!empty($class) && !empty($function)) {
            return [$projectName, $class, $function];
        }

        return null;
    }
}