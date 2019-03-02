<?php
// �ݒ荀��
$api_key = "" ;	// API Key
$api_secret = "" ;	// API Secret
$callback_url = "https://naon56.com/delete-favorite/index.html" ;	// Callback URL (���̃v���O������URL�A�h���X)

/*** [�菇4] ���[�U�[���߂��Ă��� ***/

// �F�؉�ʂ���߂��Ă����� (�F��OK)
if ( isset( $_GET['oauth_token'] ) || isset($_GET["oauth_verifier"]) ) {
	/*** [�菇5] [�菇5] �A�N�Z�X�g�[�N�����擾���� ***/

	//[���N�G�X�g�g�[�N���E�V�[�N���b�g]���Z�b�V��������Ăяo��
	session_start() ;
	$request_token_secret = $_SESSION["oauth_token_secret"] ;

	// ���N�G�X�gURL
	$request_url = "https://api.twitter.com/oauth/access_token" ;

	// ���N�G�X�g���\�b�h
	$request_method = "POST" ;

	// �L�[���쐬����
	$signature_key = rawurlencode( $api_secret ) . "&" . rawurlencode( $request_token_secret ) ;

	// �p�����[�^([oauth_signature]������)��A�z�z��Ŏw��
	$params = array(
		"oauth_consumer_key" => $api_key ,
		"oauth_token" => $_GET["oauth_token"] ,
		"oauth_signature_method" => "HMAC-SHA1" ,
		"oauth_timestamp" => time() ,
		"oauth_verifier" => $_GET["oauth_verifier"] ,
		"oauth_nonce" => microtime() ,
		"oauth_version" => "1.0" ,
	) ;

	// �z��̊e�p�����[�^�̒l��URL�G���R�[�h
	foreach( $params as $key => $value ) {
		$params[ $key ] = rawurlencode( $value ) ;
	}

	// �A�z�z����A���t�@�x�b�g���ɕ��ёւ�
	ksort($params) ;

	// �p�����[�^�̘A�z�z���[�L�[=�l&�L�[=�l...]�̕�����ɕϊ�
	$request_params = http_build_query( $params , "" , "&" ) ;

	// �ϊ������������URL�G���R�[�h����
	$request_params = rawurlencode($request_params) ;

	// ���N�G�X�g���\�b�h��URL�G���R�[�h����
	$encoded_request_method = rawurlencode( $request_method ) ;

	// ���N�G�X�gURL��URL�G���R�[�h����
	$encoded_request_url = rawurlencode( $request_url ) ;

	// ���N�G�X�g���\�b�h�A���N�G�X�gURL�A�p�����[�^��[&]�Ōq��
	$signature_data = $encoded_request_method . "&" . $encoded_request_url . "&" . $request_params ;

	// �L�[[$signature_key]�ƃf�[�^[$signature_data]�𗘗p���āAHMAC-SHA1�����̃n�b�V���l�ɕϊ�����
	$hash = hash_hmac( "sha1" , $signature_data , $signature_key , TRUE ) ;

	// base64�G���R�[�h���āA����[$signature]����������
	$signature = base64_encode( $hash ) ;

	// �p�����[�^�̘A�z�z��A[$params]�ɁA�쐬����������������
	$params["oauth_signature"] = $signature ;

	// �p�����[�^�̘A�z�z���[�L�[=�l,�L�[=�l,...]�̕�����ɕϊ�����
	$header_params = http_build_query( $params, "", "," ) ;

	// ���N�G�X�g�p�̃R���e�L�X�g���쐬����
	$context = array(
		"http" => array(
			"method" => $request_method ,	//���N�G�X�g���\�b�h
			"header" => array(	//�J�X�^���w�b�_�[
				"Authorization: OAuth " . $header_params ,
			) ,
		) ,
	) ;

	// cURL���g���ă��N�G�X�g
	$curl = curl_init() ;
	curl_setopt( $curl, CURLOPT_URL , $request_url ) ;
	curl_setopt( $curl, CURLOPT_HEADER, 1 ) ; 
	curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context["http"]["method"] ) ;	// ���\�b�h
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// �ؖ����̌��؂��s��Ȃ�
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_exec�̌��ʂ𕶎���ŕԂ�
	curl_setopt( $curl, CURLOPT_HTTPHEADER , $context["http"]["header"] ) ;	// �w�b�_�[
	curl_setopt( $curl, CURLOPT_TIMEOUT , 5 ) ;	// �^�C���A�E�g�̕b��
	$res1 = curl_exec( $curl ) ;
	$res2 = curl_getinfo( $curl ) ;
	curl_close( $curl ) ;

	// �擾�����f�[�^
	$response = substr( $res1, $res2["header_size"] ) ;	// �擾�����f�[�^(JSON�Ȃ�)
	$header = substr( $res1, 0, $res2["header_size"] ) ;	// ���X�|���X�w�b�_�[ (���؂ɗ��p�������ꍇ�ɂǂ���)

	// [cURL]�ł͂Ȃ��A[file_get_contents()]���g���ɂ͉��L�̒ʂ�ł��c
	// $response = file_get_contents( $request_url , false , stream_context_create( $context ) ) ;

	// $response�̓��e(������)��$query(�z��)�ɒ���
	// aaa=AAA&bbb=BBB �� [ "aaa"=>"AAA", "bbb"=>"BBB" ]
	$query = [] ;
	parse_str( $response, $query ) ;

	// �A�N�Z�X�g�[�N��
	// $query["oauth_token"]

	// �A�N�Z�X�g�[�N���E�V�[�N���b�g
	// $query["oauth_token_secret"]

	// ���[�U�[ID
	$query["user_id"]

	// �X�N���[���l�[��
	// $query["screen_name"]

	// �z��̓��e���o�͂��� (�{�Ԃł͕s�v)
	echo '<p>���L�̔F�؏����擾���܂����B(<a href="' . explode( "?", $_SERVER["REQUEST_URI"] )[0] . '">����1�����Ă݂�</a>)</p>' ;

	foreach ( $query as $key => $value ) {
		echo "<b>" . $key . "</b>: " . $value . "<BR>" ;
	}

// �F�؉�ʂ���߂��Ă����� (�F��NG)
} elseif ( isset( $_GET["denied"] ) ) {
	// �G���[���b�Z�[�W���o�͂��ďI��
	echo "�A�g�����ۂ��܂����B" ;
	exit ;

// ����̃A�N�Z�X
} else {
	/*** [�菇1] ���N�G�X�g�g�[�N���̎擾 ***/

	// [�A�N�Z�X�g�[�N���V�[�N���b�g] (�܂����݂��Ȃ��̂Łu�Ȃ��v)
	$access_token_secret = "" ;

	// �G���h�|�C���gURL
	$request_url = "https://api.twitter.com/oauth/request_token" ;

	// ���N�G�X�g���\�b�h
	$request_method = "POST" ;

	// �L�[���쐬���� (URL�G���R�[�h����)
	$signature_key = rawurlencode( $api_secret ) . "&" . rawurlencode( $access_token_secret ) ;

	// �p�����[�^([oauth_signature]������)��A�z�z��Ŏw��
	$params = array(
		"oauth_callback" => $callback_url ,
		"oauth_consumer_key" => $api_key ,
		"oauth_signature_method" => "HMAC-SHA1" ,
		"oauth_timestamp" => time() ,
		"oauth_nonce" => microtime() ,
		"oauth_version" => "1.0" ,
	) ;

	// �e�p�����[�^��URL�G���R�[�h����
	foreach( $params as $key => $value ) {
		// �R�[���o�b�NURL�̓G���R�[�h���Ȃ�
		if( $key == "oauth_callback" ) {
			continue ;
		}

		// URL�G���R�[�h����
		$params[ $key ] = rawurlencode( $value ) ;
	}

	// �A�z�z����A���t�@�x�b�g���ɕ��ёւ���
	ksort( $params ) ;

	// �p�����[�^�̘A�z�z���[�L�[=�l&�L�[=�l...]�̕�����ɕϊ�����
	$request_params = http_build_query( $params , "" , "&" ) ;
 
	// �ϊ������������URL�G���R�[�h����
	$request_params = rawurlencode( $request_params ) ;
 
	// ���N�G�X�g���\�b�h��URL�G���R�[�h����
	$encoded_request_method = rawurlencode( $request_method ) ;
 
	// ���N�G�X�gURL��URL�G���R�[�h����
	$encoded_request_url = rawurlencode( $request_url ) ;
 
	// ���N�G�X�g���\�b�h�A���N�G�X�gURL�A�p�����[�^��[&]�Ōq��
	$signature_data = $encoded_request_method . "&" . $encoded_request_url . "&" . $request_params ;

	// �L�[[$signature_key]�ƃf�[�^[$signature_data]�𗘗p���āAHMAC-SHA1�����̃n�b�V���l�ɕϊ�����
	$hash = hash_hmac( "sha1" , $signature_data , $signature_key , TRUE ) ;

	// base64�G���R�[�h���āA����[$signature]����������
	$signature = base64_encode( $hash ) ;

	// �p�����[�^�̘A�z�z��A[$params]�ɁA�쐬����������������
	$params["oauth_signature"] = $signature ;

	// �p�����[�^�̘A�z�z���[�L�[=�l,�L�[=�l,...]�̕�����ɕϊ�����
	$header_params = http_build_query( $params , "" , "," ) ;

	// ���N�G�X�g�p�̃R���e�L�X�g���쐬����
	$context = array(
		"http" => array(
			"method" => $request_method , // ���N�G�X�g���\�b�h (POST)
			"header" => array(			  // �J�X�^���w�b�_�[
				"Authorization: OAuth " . $header_params ,
			) ,
		) ,
	) ;

	// cURL���g���ă��N�G�X�g
	$curl = curl_init() ;
	curl_setopt( $curl, CURLOPT_URL , $request_url ) ;	// ���N�G�X�gURL
	curl_setopt( $curl, CURLOPT_HEADER, true ) ;	// �w�b�_�[���擾����
	curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context["http"]["method"] ) ;	// ���\�b�h
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// �ؖ����̌��؂��s��Ȃ�
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_exec�̌��ʂ𕶎���ŕԂ�
	curl_setopt( $curl, CURLOPT_HTTPHEADER , $context["http"]["header"] ) ;	// ���N�G�X�g�w�b�_�[�̓��e
	curl_setopt( $curl, CURLOPT_TIMEOUT , 5 ) ;	// �^�C���A�E�g�̕b��
	$res1 = curl_exec( $curl ) ;
	$res2 = curl_getinfo( $curl ) ;
	curl_close( $curl ) ;

	// �擾�����f�[�^
	$response = substr( $res1, $res2["header_size"] ) ;	// �擾�����f�[�^(JSON�Ȃ�)
	$header = substr( $res1, 0, $res2["header_size"] ) ;	// ���X�|���X�w�b�_�[ (���؂ɗ��p�������ꍇ�ɂǂ���)

	// [cURL]�ł͂Ȃ��A[file_get_contents()]���g���ɂ͉��L�̒ʂ�ł��c
	// $response = file_get_contents( $request_url , false , stream_context_create( $context ) ) ;

	// ���N�G�X�g�g�[�N�����擾�ł��Ȃ������ꍇ
	if( !$response ) {
		echo "<p>���N�G�X�g�g�[�N�����擾�ł��܂���ł����c�B$api_key��$callback_url�A������Twitter�̃A�v���P�[�V�����ɐݒ肵�Ă���Callback URL���m�F���ĉ������B</p>" ;
		exit ;
	}

	// $response�̓��e(������)��$query(�z��)�ɒ���
	// aaa=AAA&bbb=BBB �� [ "aaa"=>"AAA", "bbb"=>"BBB" ]
	$query = [] ;
	parse_str( $response, $query ) ;

	// �Z�b�V����[$_SESSION["oauth_token_secret"]]��[oauth_token_secret]��ۑ�����
	session_start() ;
	session_regenerate_id( true ) ;
	$_SESSION["oauth_token_secret"] = $query["oauth_token_secret"] ;

	/*** [�菇2] ���[�U�[��F�؉�ʂ֔�΂� ***/

	// ���[�U�[��F�؉�ʂ֔�΂� (����{�^���������ꍇ)
	header( "Location: https://api.twitter.com/oauth/authorize?oauth_token=" . $query["oauth_token"] ) ;

	// ���[�U�[��F�؉�ʂ֔�΂� (���ڈȍ~�͔F�؉�ʂ��X�L�b�v����ꍇ)
	// header( "Location: https://api.twitter.com/oauth/authenticate?oauth_token=" . $query["oauth_token"] ) ;
}