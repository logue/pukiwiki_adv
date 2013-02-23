<?php
namespace PukiWiki;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Factory{
	public static function Wiki($page){
		return new Wiki($page);
	}
	public static function Backup($page){
		return new Backup($page);
	}
}
