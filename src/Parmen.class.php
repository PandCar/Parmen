<?php

/**
 * Автор Олег Исаев
 * ВКонтакте: vk.com/id50416641
 * Skype: pandcar97
 */

class Parmen
{
	public static function encode($password = '00000', $string = '', $complicated = false)
	{
		$alphabet		= self::alphabet($password, $complicated);
		$base64			= base64_encode( gzdeflate($string, 9));
		$for_check		= substr( hash('sha512', $base64), 62, 4);
		$selection_of	= count($alphabet['0']);
		$result			= '';
		
		foreach (str_split($base64) as $value)
		{
			$result .= $alphabet[$value][mt_rand(0, $selection_of - 1)];
		}
		
		return $result.intval($complicated).$for_check;
	}
	
	public static function decode($password = '00000', $string = '')
	{
		$string		 = str_replace([' ',"\n","\t"], '', $string);
		$content	 = substr($string, 0, -5);
		$complicated = (substr($string, -5, 1) == 1);
		$check		 = substr($string, -4, 4);
		$alphabet	 = self::alphabet($password, $complicated);
		$result		 = '';
		
		foreach (str_split($content, ($complicated ? 3 : 2)) as $value)
		{
			foreach ($alphabet as $letter => $code_list)
			{
				if (in_array($value, $code_list))
				{
					$result .= $letter;
					
					break;
				}
			}
		}
		
		if (substr( hash('sha512', $result), 62, 4) == $check)
		{
			return gzinflate( base64_decode($result) );
		}
		
		return false;
	}
	
	protected static function alphabet($password, $complicated)
	{
		$level = $complicated ? ['count' => 10, 'chars' => 3] : ['count' => 3, 'chars' => 2];
		
		$hash_pass	= hash('sha512', $password);
		$chars		= array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'), ['/','=','+']);
		$code_rand	= substr( preg_replace('/[^0-9]+/i', '', $hash_pass), 0, 5);
		
		self::seeded_unshuffle($chars, $code_rand);
		
		$cipher		= [];
		$count_arr	= count($chars) * $level['count'];
		$i			= 1;
		
		while ($count_arr > count($cipher))
		{
			$gen = array_merge(str_split(hash('sha512', $hash_pass), 2), str_split(hash('sha512', $i), 2));
			
			self::seeded_unshuffle($gen, $code_rand);
			
			$dust = str_split(hash('sha512', implode('', $gen)), $level['chars']);
			
			foreach ($dust as $value)
			{
				if (strlen($value) == $level['chars'] && !in_array($value, $cipher))
				{
					$cipher[] = $value;
				}
			}
			$i++;
		}
		
		$cipher	= array_chunk($cipher, $level['count']);
		$result	= [];
		
		foreach ($chars as $key => $value)
		{
			$result[$value] = $cipher[$key];
		}
		
		return $result;
	}
	
	protected static function seeded_unshuffle(&$items, $seed)
	{
		$items	 = array_values($items);
		$indices = [];

		mt_srand($seed);
		
		for ($i = count($items) - 1; $i > 0; $i--)
		{
			$indices[$i] = mt_rand(0, $i);
		}
		
		mt_srand();

		foreach (array_reverse($indices, true) as $i => $j)
		{
			list($items[$i], $items[$j]) = [$items[$j], $items[$i]];
		}
	}
}
