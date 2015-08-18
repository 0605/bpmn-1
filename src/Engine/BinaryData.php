<?php

/*
 * This file is part of KoolKode BPMN.
 *
 * (c) Martin Schröder <m.schroeder2007@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KoolKode\BPMN\Engine;

use Psr\Http\Message\StreamInterface;

class BinaryData
{
	const TYPE_RAW = 1;
	
	const TYPE_HEX = 2;
	
	protected $data;
	
	protected $level;
	
	public function __construct($data, $level = 1)
	{
		if(is_resource($data))
		{
			$data = stream_get_contents($data);
		}
		elseif($data instanceof StreamInterface)
		{
			$data = $data->getContents();
		}
		
		$this->data = (string)$data;
		$this->level = (int)$level;
	}
	
	public function __toString()
	{
		return $this->data;
	}
	
	public function __debugInfo()
	{
		return [
			'length' => strlen($this->data),
			'compressionLevel' => $this->level
		];
	}
	
	public function encode()
	{
		return gzcompress($this->data, $this->level);
	}
	
	public static function decode($input)
	{
		if(is_resource($input))
		{
			$input = stream_get_contents($input);
		}
		
		if($input === NULL || '' === (string)$input)
		{
			return NULL;
		}
		
		return gzuncompress($input);
	}
}