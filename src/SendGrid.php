<?php
namespace SilexFriends\SendGrid;

use SendGrid as Sender;
use SendGrid\Email;
use SendGrid\Mail;
use SendGrid\Exception;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * SendGrid Service Provider
 *
 * @author Thiago Paes <mrprompt@gmail.com>
 */
final class SendGrid implements SendGridInterface, ServiceProviderInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * SendGrid constructor.
     *
     * @param string $name
     * @param string $key
     */
    public function __construct(string $name, string $key)
    {
        $this->config = [
            'api_name' => $name,
            'api_key'  => $key,
        ];
    }

    /**
     * (non-PHPdoc)
     * @see \Silex\ServiceProviderInterface::register()
     */
    public function register(Application $app)
    {
        $app[static::NAME] = $app->protect(
            function ($to, $from, $template, $tags) {
                return $this->send($to, $from, $template, $tags);
            }
        );
    }

    /**
     * (non-PHPdoc)
     * @see \Silex\ServiceProviderInterface::boot()
     */
    public function boot(Application $app)
    {
        // :)
    }

    /**
     * @inheritdoc
     */
    public function send(string $to, string $from, string $template, array $tags = []): bool
    {
        try {
            $apiKey = $this->config['api_key'];

            $mail = new Mail($from, ' ', $to, ' ', $tags);
            $mail->setTemplateId($template);

            $sender = new Sender($apiKey);
            $sender->client->mail()->send($mail);

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
}
