<?php
// An O(NP) Sequence Comparison Algorithm" for PHP
// Copyright (c) 2012 Logue <logue@hotmail.co.jp> All rights reserved.
// License: BSD license
// based on https://github.com/cubicdaiya/onp
namespace PukiWiki\Diff;

use PukiWiki\Utility;

ini_set("memory_limit",-1);

/**
 * The algorithm implemented here is based on "An O(NP) Sequence Comparison Algorithm"
 * by described by Sun Wu, Udi Manber and Gene Myers
 */
class Diff{
	const SES_DELETE = '-';
	const SES_COMMON = ' ';
	const SES_ADD    = '+';

	private $a, $b, $m, $n;
	private $editdis = 0;
	private $reverse = false;
	private $pathposi = array();
	private $path = array();
	private $ses = array();
	private $lcs = '';

	/**
	 * コンストラクタ
	 * @param array $a 元データー
	 * @param array $b 新しいデーター
	 */
	public function __construct(/* array */$a, /* array */$b){
		$this->a = is_array($a) ? $a : explode("\n", $a);
		$this->b = is_array($a) ? $b : explode("\n", $b);
		$this->m = count($this->a);
		$this->n = count($this->b);

		if ($this->m >= $this->n){
			$this->a = is_array($b) ? $b : explode("\n", $b);
			$this->b = is_array($a) ? $a : explode("\n", $a);
			$this->m = count($this->b);
			$this->n = count($this->a);
			$this->reverse = true;
		}
		self::compose();
	}

	private function compose(){
		$p = -1;
		$delta  = $this->n - $this->m;
		$size   = $this->m + $this->n + 3;
		$offset = $this->m + 1;

		$fp = array_fill(0, $size, -1);
		$this->path = array_fill(0, $size, -1);
		do {
			++$p;
			for ($k=-$p; $k<=$delta-1; ++$k) {
				$fp[$k+$offset] = self::snake($k, $fp[$k-1+$offset]+1, $fp[$k+1+$offset]);
			}
			for ($k=$delta+$p; $k>=$delta+1; --$k) {
				$fp[$k+$offset] = self::snake($k, $fp[$k-1+$offset]+1, $fp[$k+1+$offset]);
			}
			$fp[$delta+$offset] = self::snake($delta, $fp[$delta-1+$offset]+1, $fp[$delta+1+$offset]);
		} while($fp[$delta+$offset] !== $this->n);

		$this->editdis = $delta + 2 * $p;

		$r = $this->path[$delta+$offset];

		$epc = array();
		while ($r !== -1) {
			$_pathposi = $this->pathposi[$r];
			$epc[] = array(
				'x'=>$_pathposi['x'],
				'y'=>$_pathposi['y'],
				'k'=>null
			);
			$r = $_pathposi['k'];
		}
		self::recordseq($epc);
	}

	private function snake($k, $p, $pp){
		$offset = $this->m+1;
		$r = ($p > $pp) ? $this->path[$k-1+$offset] : $this->path[$k+1+$offset];

		$y = max($p, $pp);
		$x = $y - $k;
		while ($x < $this->m && $y < $this->n &&
			 (isset($this->a[$x]) && isset($this->b[$y]) && $this->a[$x] === $this->b[$y])) {
			$x++;
			$y++;
		}

		$this->path[$k+$offset] = count($this->pathposi);
		$this->pathposi[] = array('x'=>$x, 'y'=>$y, 'k'=>$r);
		return $y;
	}

	private function recordseq ($epc) {
		$x_idx  = $y_idx  = 1;
		$px_idx = $py_idx = 0;
		for ($i = count($epc) - 1; $i>=0; --$i) {
			while($px_idx < $epc[$i]['x'] || $py_idx < $epc[$i]['y']) {
				if ($epc[$i]['y'] - $epc[$i]['x'] > $py_idx - $px_idx) {
					if (isset($this->b[$py_idx])){
						$str = isset($this->b[$py_idx]) ? rtrim($this->b[$py_idx]) : '';
						if ($this->reverse) {
							$this->ses[] = array(self::SES_DELETE, $str);
						} else {
							$this->ses[] = array(self::SES_ADD,    $str);
						}
					}
					++$y_idx;
					++$py_idx;
				} else if ($epc[$i]['y'] - $epc[$i]['x'] < $py_idx - $px_idx) {
					if (isset($this->a[$px_idx])){
						$str = isset($this->a[$px_idx]) ? rtrim($this->a[$px_idx]) : '';
						if ($this->reverse) {
							$this->ses[] = array(self::SES_ADD,    $str);
						} else {
							$this->ses[] = array(self::SES_DELETE, $str);
						}
					}
					++$x_idx;
					++$px_idx;
				} else {
					$str = isset($this->a[$px_idx]) ? rtrim($this->a[$px_idx]) : '';
					if (isset($this->a[$px_idx])) {
						$this->ses[] =     array(self::SES_COMMON, $str);
						$this->lcs += $this->a[$px_idx];
					}
					++$x_idx;
					++$y_idx;
					++$px_idx;
					++$py_idx;
				}
				unset($str);
			}
		}
	}
	public function getEditDistance(){
		return $this->editdis;
	}
	public function getLcs() {
		return $this->lcs;
	}
	public function getSes() {
		return $this->ses;
	}
	public function getDiff(){
		foreach ($this->ses as $k=>$v){
			$ret[$k] = $v[0] . $v[1];
		}
		return $ret;
	}
	public function getHtml(){
		foreach ($this->ses as $k=>$v){
			$str = Utility::htmlsc($v[1]);
			switch($v[0]){
				case self::SES_ADD:
					$ret[] = '+<ins class="diff_added">' . $str . '</ins>';
					break;
				case self::SES_DELETE:
					$ret[] = '-<del class="diff_removed">' . $str . '</del>';
					break;
				default:
					$ret[] = ' ' . $str;
					break;
			}
		}
		return '<pre class="sh sunlight-highlight-diff">' . "\n" . join("\n", $ret) . '</pre>' . "\n";
	}
	public function __toString(){
		return join("\n",self::getDiff());
	}
}