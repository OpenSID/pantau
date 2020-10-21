<?php
	function ambil_bukti($filename, $ukuran='')
	{
		$file = base_url() . 'uploads/' . $filename;
		return $file;
	}

	function hapus_bukti($filename)
	{
		if (! unlink('uploads/' . $filename))
		{
			log_message('error', "Tidak bisa hapus bukti lama: ".'uploads/' . $filename);
			return false;
		}

	}
?>
