<?php

namespace Rocco\PhpSoapClient\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rocco\PhpSoapClient\Command\Base\SoapCommand;


class CallMethodCommand extends SoapCommand
{
  protected function configure()
  {
    parent::configure();

    $this->setName('call');
    $this->setDescription('Call the remote service with the `method` specified and output the reponse to stdout.');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    if (false === isset($method))
    {
      throw new \Exception('You must specify a method name to call');
    }

    if ($this->has_option('use-editor'))
    {
      $editor = $_SERVER['EDITOR'];
      $empty_request = $this->remote_service->__getRequestXmlForMethod($method);

      $this->log->info('Starting editor: ' . $editor);
      $request_xml = $this->read_from_editor($editor, $empty_request);
    }
    else
    {
      $request_xml = $this->read_from_stdin(true);
    }

    if (null === $request_xml)
    {
      $this->log->info('Create xml request below and finish with ctrl+d');
      $request_xml = $this->read_from_stdin(false);
    }

    try
    {
      $this->log->info('Calling method %s on the remote', $method);
      $t1 = microtime(true);

      if (true === $this->has_option('xml'))
      {
        $response = $this->remote_service->__getResponseXmlForMethod($method, $request_xml);
      }
      else
      {
        $response = $this->remote_service->__getResponseObjectForMethod($method, $request_xml);
      }

      $this->log->info('Calling method took %s seconds', microtime(true) - $t1);
      unset($t1);

      print_r($response);

      return 0;
    }
    catch (Exception $e)
    {
      $this->log->error(sprintf('Error while calling %s on %s', $method, $endpoint));
      throw $e;
    }
  }

  protected function read_from_editor($editor, $contents=null)
  {
    $temp_file = tmpfile();
    $temp_file_info = stream_get_meta_data($temp_file);

    if (false === is_null($contents))
    {
      fwrite($temp_file, $contents);
    }

    $temp_filename = $temp_file_info['uri'];

    $this->log->debug('Start editing file ' . $temp_filename);

    system("$editor $temp_filename > `tty`");

    $input_xml = $this->read_from_file($temp_filename);
    fclose($temp_file);

    if (0 === strcmp((string)$contents, $input_xml))
    {
      $this->log->debug('File wasn\'t modified');
    }
    else
    {
      $this->log->debug('File was modified using the editor');
    }

    return $input_xml;
  }

  protected function read_from_file($filename, $length=2048)
  {
    $file = fopen($filename, 'r');
    $contents = fread($file, $length);
    fclose($blaat);

    return $contents;
  }
}
