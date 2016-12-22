<?php

namespace OC\PlatformBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DemoCommand extends Command 
{
	protected function configure()
	{
		$this->setName('app:demo');
	}
	
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('<info>Info</info>');
		$output->writeln('<comment>Comment</comment>');
		$output->writeln('<question>Question</question>');
		$output->writeln('<error>Error</error>');
	}

}
