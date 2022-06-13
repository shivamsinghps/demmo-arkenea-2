<?php
/**
 * Author: Anton Orlov
 * Date: 22.03.2018
 * Time: 11:06
 */

namespace FMT\InfrastructureBundle\Helper;

class NotificationHelper
{
    const DEFAULT_MIME = "text/html";
    const DEFAULT_CHARSET = "UTF-8";

    /** @var \Swift_Mailer */
    private static $mailer;

    /** @var string */
    private static $senderName;

    /** @var string */
    private static $senderAddress;

    /**
     * @param \Swift_Mailer $mailer
     * @param string $senderName
     * @param string $senderAddress
     */
    public static function init(\Swift_Mailer $mailer, $senderName, $senderAddress)
    {
        if (empty(self::$mailer)) {
            self::$mailer = $mailer;
        }

        self::$senderName = $senderName;
        self::$senderAddress = $senderAddress;
    }

    /**
     * @param string $subject
     * @param string $message
     * @param string $recipient
     * @param array $attachments
     * @return int
     */
    public static function submit($subject, $message, $recipient, $attachments = [])
    {
        if (empty(self::$mailer)) {
            return 0;
        }

        $message = new \Swift_Message($subject, $message, self::DEFAULT_MIME, self::DEFAULT_CHARSET);
        $message->addFrom(self::$senderAddress, self::$senderName);
        list($rcptAddress, $rcptName) = self::parseAddress($recipient);
        $message->setTo($rcptAddress, $rcptName);

        if (!empty($attachments)) {
            $objects = [];
            foreach ($attachments as $attachment) {
                $objects[] = self::getAttachment($attachment);
            }
            array_map([$message, "attach"], $objects);
        }

        LogHelper::info(
            "Creating notification from %s to %s with subject: %s",
            self::$senderAddress,
            $recipient,
            $subject
        );

        return self::$mailer->send($message);
    }

    /**
     * Method submits message from HTML template, it tries to extract message subject from HTML <title> tag
     *
     * @param string $message
     * @param string $recipient
     * @param array $attachments
     * @return int
     */
    public static function submitFromTemplate($message, $recipient, $attachments = [])
    {
        $subject = null;
        $start = strpos(strtolower($message), "<title>");

        if ($start !== false) {
            $end = strpos(strtolower($message), "</title>", $start);
            if ($end !== false) {
                $subject = substr($message, $start + 7, $end - $start - 7);
            }
        }

        return self::submit($subject, $message, $recipient, $attachments);
    }

    /**
     * @param mixed $attachment
     * @return \Swift_Attachment
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    protected static function getAttachment($attachment)
    {
        $functions = array_keys(array_filter([
            "getMimeFromPath" => is_string($attachment) && file_exists($attachment),
            "getMimeFromFileInfo" => $attachment instanceof \SplFileInfo,
            "getMimeFromResource" => is_resource($attachment) && get_resource_type($attachment) == "stream",
            "getMimeFromString" => true
        ]));

        return call_user_func(__CLASS__ . "::" . array_shift($functions), $attachment);
    }

    /**
     * @param string $data
     * @return \Swift_Attachment
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    protected static function getMimeFromString($data)
    {
        return new \Swift_Attachment((string) $data, uniqid() . ".txt", "text/plain");
    }

    /**
     * @param string $path
     * @return \Swift_Attachment
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    protected static function getMimeFromPath($path)
    {
        $filename = basename($path);
        $mime = mime_content_type($path);
        $stream = new \Swift_ByteStream_FileByteStream($path);

        return new \Swift_Attachment($stream, $filename, $mime);
    }

    /**
     * @param \SplFileInfo $info
     * @return \Swift_Attachment
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    protected static function getMimeFromFileInfo(\SplFileInfo $info)
    {
        return self::getMimeFromPath($info->getPathname());
    }

    /**
     * @param $resource
     * @return \Swift_Attachment
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    protected static function getMimeFromResource($resource)
    {
        $filename = uniqid() . ".txt";
        $mime = "text/plain";
        $meta = stream_get_meta_data($resource);

        if (isset($meta["uri"]) && !empty($meta["uri"])) {
            $filename = basename($meta["uri"]);
            if (file_exists($meta["uri"])) {
                $mime = mime_content_type($meta["uri"]);
            } else {
                $mime = "application/octet-stream";
            }
        }

        rewind($resource);

        return new \Swift_Attachment(stream_get_contents($resource), $filename, $mime);
    }

    /**
     * @param string $address
     * @return string[]
     */
    protected static function parseAddress($address)
    {
        $email = $address;
        $name = null;
        if (strpos($email, "<") !== false) {
            list($name, $email) = explode("<", $address, 2);
            $name = trim($name);
            $email = trim(str_replace(["<", ">"], ["", ""], $email));
        }
        return [$email, $name];
    }
}
