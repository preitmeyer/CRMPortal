<?php

require_once ('AdapterOnline.php');
require_once ('AdapterHosted.php');

abstract class AdapterFactory
{
	static function CreateAdapter()
	{
		if (defined('CRMTYPE'))
		{
			switch (CRMTYPE)
			{
				case 'Online':
					return new AdapterOnline();
				case 'Hosted':
					return new AdapterHosted(); 
			}
		}
	}
}
