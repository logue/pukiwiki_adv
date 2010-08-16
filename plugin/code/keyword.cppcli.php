<?php
/**
 * C++/CLI キーワード定義ファイル
 */

// C/C++ の設定を取り込む
require_once('keyword.c.php');

// スペースを含むキーワード
$code_space_keyword = Array(
	'enum class' => 2,
	'enum struct' => 2,
	'for each' => 2,
	'interface class' => 2,
	'interface struct' => 2,
	'ref class' => 2,
	'ref struct' => 2,
	'value class' => 2,
	'value struct' => 2,
	);

// 通常キーワード追加
$code_keyword += Array(
	// 文脈依存キーワード
	'abstract' => 2,
	'delegate' => 2,
	'event' => 2,
	'finally' => 2,
	'generic' => 2,
	'initonly' => 2,
	'internal' => 2,
	'literal' => 2,
	'override' => 2,
	'property' => 2,
	'sealed' => 2,
	'where' => 2,

	// その他のキーワード
	'gcnew' => 2,
	'in' => 2,
	'nullptr' => 2,

	// cli::* キーワード
	'array' => 2,
	'interior_ptr' => 2,
	'pin_ptr' => 2,
	'safe_cast' => 2,

	'#using' => 3,
	);

?>