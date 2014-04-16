<?php

function convert_value($value)
{
	return Converter::value($value)->to('currency.usd')->format();
}
