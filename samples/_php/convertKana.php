<?php
function convertJapaneseKun($s)
{
	$s=str_replace("PP","っP",$s);
	$s=str_replace("SS","っS",$s);
	$s=str_replace("TT","っT",$s);

	$s=str_replace("AA","Aあ",$s);
	$s=str_replace("II","Iい",$s);
	$s=str_replace("UU","Uう",$s);
	$s=str_replace("EE","Eえ",$s);
	$s=str_replace("OO","Oお",$s);

	$s=str_replace("KYA","きゃ",$s);
	$s=str_replace("KYU","きゅ",$s);
	$s=str_replace("KYO","きょ",$s);

	$s=str_replace("KA","か",$s);
	$s=str_replace("KI","き",$s);
	$s=str_replace("KU","く",$s);
	$s=str_replace("KE","け",$s);
	$s=str_replace("KO","こ",$s);

	$s=str_replace("GYA","ぎゃ",$s);
	$s=str_replace("GYU","ぎゅ",$s);
	$s=str_replace("GYO","ぎょ",$s);
	
	$s=str_replace("GA","が",$s);
	$s=str_replace("GI","ぎ",$s);
	$s=str_replace("GU","ぐ",$s);
	$s=str_replace("GE","げ",$s);
	$s=str_replace("GO","ご",$s);

	$s=str_replace("CHA","ちゃ",$s);
	$s=str_replace("CHU","ちゅ",$s);
	$s=str_replace("CHO","ちょ",$s);
	
	// tsu before su
	$s=str_replace("TA","た",$s);
	$s=str_replace("CHI","ち",$s);
	$s=str_replace("TSU","つ",$s);
	$s=str_replace("TE","て",$s);
	$s=str_replace("TO","と",$s);

	$s=str_replace("SHA","しゃ",$s);
	$s=str_replace("SHU","しゅ",$s);
	$s=str_replace("SHO","しょ",$s);

	$s=str_replace("SA","さ",$s);
	$s=str_replace("SHI","し",$s);
	$s=str_replace("SU","す",$s);
	$s=str_replace("SE","せ",$s);
	$s=str_replace("SO","そ",$s);

	$s=str_replace("JA","じゃ",$s);
	$s=str_replace("JU","じゅ",$s);
	$s=str_replace("JO","じょ",$s);
	
	$s=str_replace("ZA","ざ",$s);
	$s=str_replace("JI","じ",$s);
	$s=str_replace("ZU","ず",$s);
	$s=str_replace("ZE","ぜ",$s);
	$s=str_replace("ZO","ぞ",$s);

	$s=str_replace("JA","ぢゃ",$s);
	$s=str_replace("JU","ぢゅ",$s);
	$s=str_replace("JO","ぢょ",$s);

	$s=str_replace("DA","だ",$s);
	$s=str_replace("JI","ぢ",$s);
	$s=str_replace("ZU","ず",$s);
	$s=str_replace("DE","で",$s);
	$s=str_replace("DO","ど",$s);
		
	$s=str_replace("NYA","にゃ",$s);
	$s=str_replace("NYU","にゅ",$s);
	$s=str_replace("NYO","にょ",$s);
	
	$s=str_replace("NA","な",$s);
	$s=str_replace("NI","に",$s);
	$s=str_replace("NU","ぬ",$s);
	$s=str_replace("NE","ね",$s);
	$s=str_replace("NO","の",$s);

	$s=str_replace("HYA","ひゃ",$s);
	$s=str_replace("HYU","ひゅ",$s);
	$s=str_replace("HYO","ひょ",$s);

	$s=str_replace("HA","は",$s);
	$s=str_replace("HI","ひ",$s);
	$s=str_replace("FU","ふ",$s);
	$s=str_replace("HE","へ",$s);
	$s=str_replace("HO","ほ",$s);

	$s=str_replace("BYA","びゃ",$s);
	$s=str_replace("BYU","びゅ",$s);
	$s=str_replace("BYO","びょ",$s);

	$s=str_replace("BA","ば",$s);
	$s=str_replace("BI","び",$s);
	$s=str_replace("BU","ぶ",$s);
	$s=str_replace("BE","べ",$s);
	$s=str_replace("BO","ぼ",$s);

	$s=str_replace("PYA","ぴゃ",$s);
	$s=str_replace("PYU","ぴゅ",$s);
	$s=str_replace("PYO","ぴょ",$s);

	$s=str_replace("PA","ぱ",$s);
	$s=str_replace("PI","ぴ",$s);
	$s=str_replace("PU","ぷ",$s);
	$s=str_replace("PE","ぺ",$s);
	$s=str_replace("PO","ぽ",$s);

	$s=str_replace("MYA","みゃ",$s);
	$s=str_replace("MYU","みゅ",$s);
	$s=str_replace("MYO","みょ",$s);
	
	$s=str_replace("MA","ま",$s);
	$s=str_replace("MI","み",$s);
	$s=str_replace("MU","む",$s);
	$s=str_replace("ME","め",$s);
	$s=str_replace("MO","も",$s);

	$s=str_replace("RYA","りゃ",$s);
	$s=str_replace("RYU","りゅ",$s);
	$s=str_replace("RYO","りょ",$s);

	$s=str_replace("RA","ら",$s);
	$s=str_replace("RI","り",$s);
	$s=str_replace("RU","る",$s);
	$s=str_replace("RE","れ",$s);
	$s=str_replace("RO","ろ",$s);

	// ya after [x]ya
	$s=str_replace("YA","や",$s);
	$s=str_replace("YU","ゆ",$s);
	$s=str_replace("YO","よ",$s);
	
	$s=str_replace("WA","わ",$s);
	$s=str_replace("WO","を",$s);

	$s=str_replace("A","あ",$s);
	$s=str_replace("I","い",$s);
	$s=str_replace("U","う",$s);
	$s=str_replace("E","え",$s);
	$s=str_replace("O","お",$s);
	
	$s=str_replace("N","ん",$s);
	
	return $s;
}
function convertJapaneseOn($s)
{
	$s=str_replace("PP","ッP",$s);
	$s=str_replace("SS","ッS",$s);
	$s=str_replace("TT","ッT",$s);

	$s=str_replace("AA","Aア",$s);
	$s=str_replace("II","Iイ",$s);
	$s=str_replace("UU","Uウ",$s);
	$s=str_replace("EE","Eエ",$s);
	$s=str_replace("OO","Oオ",$s);
	
	$s=str_replace("KYA","キャ",$s);
	$s=str_replace("KYU","キュ",$s);
	$s=str_replace("KYO","キョ",$s);
	$s=str_replace("GYA","ギャ",$s);
	$s=str_replace("GYU","ギュ",$s);
	$s=str_replace("GYO","ギョ",$s);

	$s=str_replace("KA","カ",$s);
	$s=str_replace("KI","キ",$s);
	$s=str_replace("KU","ク",$s);
	$s=str_replace("KE","ケ",$s);
	$s=str_replace("KO","コ",$s);

	$s=str_replace("GA","ガ",$s);
	$s=str_replace("GI","ギ",$s);
	$s=str_replace("GU","グ",$s);
	$s=str_replace("GE","ゲ",$s);
	$s=str_replace("GO","ゴ",$s);

	// tsu before su
	$s=str_replace("CHA","チャ",$s);
	$s=str_replace("CHU","チュ",$s);
	$s=str_replace("CHO","チョ",$s);

	$s=str_replace("TA","タ",$s);
	$s=str_replace("CHI","チ",$s);
	$s=str_replace("TSU","ツ",$s);
	$s=str_replace("TE","テ",$s);
	$s=str_replace("TO","ト",$s);

	$s=str_replace("SHA","シャ",$s);
	$s=str_replace("SHU","シュ",$s);
	$s=str_replace("SHO","ショ",$s);
	
	$s=str_replace("SA","サ",$s);
	$s=str_replace("SHI","シ",$s);
	$s=str_replace("SU","ス",$s);
	$s=str_replace("SE","セ",$s);
	$s=str_replace("SO","ソ",$s);

	$s=str_replace("JA","ジャ",$s);
	$s=str_replace("JU","ジュ",$s);
	$s=str_replace("JO","ジョ",$s);
	
	$s=str_replace("ZA","ザ",$s);
	$s=str_replace("JI","ジ",$s);
	$s=str_replace("ZU","ズ",$s);
	$s=str_replace("ZE","ゼ",$s);
	$s=str_replace("ZO","ゾ",$s);

	$s=str_replace("CHA","チャ",$s);
	$s=str_replace("CHU","チュ",$s);
	$s=str_replace("CHO","チョ",$s);

	$s=str_replace("TA","タ",$s);
	$s=str_replace("CHI","チ",$s);
	$s=str_replace("TSU","ツ",$s);
	$s=str_replace("TE","テ",$s);
	$s=str_replace("TO","ト",$s);

	$s=str_replace("JA","ヂャ",$s);
	$s=str_replace("JU","ヂュ",$s);
	$s=str_replace("JO","ヂョ",$s);

	$s=str_replace("DA","ダ",$s);
	$s=str_replace("JI","ヂ",$s);
	$s=str_replace("ZU","ズ",$s);
	$s=str_replace("DE","デ",$s);
	$s=str_replace("DO","ド",$s);
		
	$s=str_replace("NYA","ニャ",$s);
	$s=str_replace("NYU","ニュ",$s);
	$s=str_replace("NYO","ニョ",$s);

	$s=str_replace("NA","ナ",$s);
	$s=str_replace("NI","ニ",$s);
	$s=str_replace("NU","ヌ",$s);
	$s=str_replace("NE","ネ",$s);
	$s=str_replace("NO","ノ",$s);

	$s=str_replace("HYA","ヒャ",$s);
	$s=str_replace("HYU","ヒュ",$s);
	$s=str_replace("HYO","ヒョ",$s);
	
	$s=str_replace("HA","ハ",$s);
	$s=str_replace("HI","ヒ",$s);
	$s=str_replace("FU","フ",$s);
	$s=str_replace("HE","ヘ",$s);
	$s=str_replace("HO","ホ",$s);

	$s=str_replace("BYA","ビャ",$s);
	$s=str_replace("BYU","ビュ",$s);
	$s=str_replace("BYO","ビョ",$s);

	$s=str_replace("BA","バ",$s);
	$s=str_replace("BI","ビ",$s);
	$s=str_replace("BU","ブ",$s);
	$s=str_replace("BE","ベ",$s);
	$s=str_replace("BO","ボ",$s);

	$s=str_replace("PYA","ピャ",$s);
	$s=str_replace("PYU","ピュ",$s);
	$s=str_replace("PYO","ピョ",$s);

	$s=str_replace("PA","パ",$s);
	$s=str_replace("PI","ピ",$s);
	$s=str_replace("PU","プ",$s);
	$s=str_replace("PE","ペ",$s);
	$s=str_replace("PO","ポ",$s);

	$s=str_replace("MYA","ミャ",$s);
	$s=str_replace("MYU","ミュ",$s);
	$s=str_replace("MYO","ミョ",$s);

	$s=str_replace("MA","マ",$s);
	$s=str_replace("MI","ミ",$s);
	$s=str_replace("MU","ム",$s);
	$s=str_replace("ME","メ",$s);
	$s=str_replace("MO","モ",$s);

	$s=str_replace("RYA","リャ",$s);
	$s=str_replace("RYU","リュ",$s);
	$s=str_replace("RYO","リョ",$s);

	$s=str_replace("RA","ラ",$s);
	$s=str_replace("RI","リ",$s);
	$s=str_replace("RU","ル",$s);
	$s=str_replace("RE","レ",$s);
	$s=str_replace("RO","ロ",$s);

	// ya after [x]ya
	$s=str_replace("YA","ヤ",$s);
	$s=str_replace("YU","ユ",$s);
	$s=str_replace("YO","ヨ",$s);

	$s=str_replace("WA","ワ",$s);
	$s=str_replace("WO","ヲ",$s);

	$s=str_replace("A","ア",$s);
	$s=str_replace("I","イ",$s);
	$s=str_replace("U","ウ",$s);
	$s=str_replace("E","エ",$s);
	$s=str_replace("O","オ",$s);

	$s=str_replace("N","ン",$s);

	return $s;
}
?>