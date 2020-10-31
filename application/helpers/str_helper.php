<?php

	function print_if($a, $b, $str = 'selected')
	{
		if ($a == $b)
		{
			echo $str;
		}
	}

	function selected($a, $b)
	{
		print_if($a, $b);
	}

?>