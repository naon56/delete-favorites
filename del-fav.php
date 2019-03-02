<?php
/**************************************************

	[GET favorites/list]�̂������v���O����

	�F�ؕ���: �A�N�Z�X�g�[�N��

	�z�z: SYNCER
	�����h�L�������g: https://dev.twitter.com/rest/reference/get/favorites/list
	���{�����y�[�W: https://syncer.jp/Web/API/Twitter/REST_API/GET/favorites/list/

**************************************************/

	//�l���g�������ꍇ��require���𖄂ߍ���
	var_dump(require 'login.php');

	// �ݒ�
	$api_key = '' ;		// API�L�[
	$api_secret = '' ;		// API�V�[�N���b�g
	$access_token = '' ;		// �A�N�Z�X�g�[�N��
	$access_token_secret = '' ;		// �A�N�Z�X�g�[�N���V�[�N���b�g
	$request_url = 'https://api.twitter.com/1.1/favorites/list.json' ;		// �G���h�|�C���g
	$request_method = 'GET' ;

	// �p�����[�^A (�I�v�V����)
	$params_a = array(
		"user_id",
//		"screen_name" => "arayutw",
		"count" => "100",
//		"since_id" => "643299864344788992",
//		"max_id" => "643299864344788992",
//		"include_entities" => "true",
	) ;

	// �L�[���쐬���� (URL�G���R�[�h����)
	$signature_key = rawurlencode( $api_secret ) . '&' . rawurlencode( $access_token_secret ) ;

	// �p�����[�^B (�����̍ޗ��p)
	$params_b = array(
		'oauth_token' => $access_token ,
		'oauth_consumer_key' => $api_key ,
		'oauth_signature_method' => 'HMAC-SHA1' ,
		'oauth_timestamp' => time() ,
		'oauth_nonce' => microtime() ,
		'oauth_version' => '1.0' ,
	) ;

	// �p�����[�^A�ƃp�����[�^B���������ăp�����[�^C�����
	$params_c = array_merge( $params_a , $params_b ) ;

	// �A�z�z����A���t�@�x�b�g���ɕ��ёւ���
	ksort( $params_c ) ;

	// �p�����[�^�̘A�z�z���[�L�[=�l&�L�[=�l...]�̕�����ɕϊ�����
	$request_params = http_build_query( $params_c , '' , '&' ) ;

	// �ꕔ�̕�������t�H���[
	$request_params = str_replace( array( '+' , '%7E' ) , array( '%20' , '~' ) , $request_params ) ;

	// �ϊ������������URL�G���R�[�h����
	$request_params = rawurlencode( $request_params ) ;

	// ���N�G�X�g���\�b�h��URL�G���R�[�h����
	// �����ł́AURL������[?]�ȉ��͕t���Ȃ�����
	$encoded_request_method = rawurlencode( $request_method ) ;
 
	// ���N�G�X�gURL��URL�G���R�[�h����
	$encoded_request_url = rawurlencode( $request_url ) ;
 
	// ���N�G�X�g���\�b�h�A���N�G�X�gURL�A�p�����[�^��[&]�Ōq��
	$signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params ;

	// �L�[[$signature_key]�ƃf�[�^[$signature_data]�𗘗p���āAHMAC-SHA1�����̃n�b�V���l�ɕϊ�����
	$hash = hash_hmac( 'sha1' , $signature_data , $signature_key , TRUE ) ;

	// base64�G���R�[�h���āA����[$signature]����������
	$signature = base64_encode( $hash ) ;

	// �p�����[�^�̘A�z�z��A[$params]�ɁA�쐬����������������
	$params_c['oauth_signature'] = $signature ;

	// �p�����[�^�̘A�z�z���[�L�[=�l,�L�[=�l,...]�̕�����ɕϊ�����
	$header_params = http_build_query( $params_c , '' , ',' ) ;

	// ���N�G�X�g�p�̃R���e�L�X�g
	$context = array(
		'http' => array(
			'method' => $request_method , // ���N�G�X�g���\�b�h
			'header' => array(			  // �w�b�_�[
				'Authorization: OAuth ' . $header_params ,
			) ,
		) ,
	) ;

	// �p�����[�^������ꍇ�AURL�̖����ɒǉ�
	if( $params_a ) {
		$request_url .= '?' . http_build_query( $params_a ) ;
	}

	// �I�v�V����������ꍇ�A�R���e�L�X�g��POST�t�B�[���h���쐬���� (GET�̏ꍇ�͕s�v)
//	if( $params_a ) {
//		$context['http']['content'] = http_build_query( $params_a ) ;
//	}

	// cURL���g���ă��N�G�X�g
	$curl = curl_init() ;
	curl_setopt( $curl, CURLOPT_URL , $request_url ) ;
	curl_setopt( $curl, CURLOPT_HEADER, 1 ) ; 
	curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context['http']['method'] ) ;	// ���\�b�h
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// �ؖ����̌��؂��s��Ȃ�
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_exec�̌��ʂ𕶎���ŕԂ�
	curl_setopt( $curl, CURLOPT_HTTPHEADER , $context['http']['header'] ) ;	// �w�b�_�[
//	if( isset( $context['http']['content'] ) && !empty( $context['http']['content'] ) ) {		// GET�̏ꍇ�͕s�v
//		curl_setopt( $curl , CURLOPT_POSTFIELDS , $context['http']['content'] ) ;	// ���N�G�X�g�{�f�B
//	}
	curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;	// �^�C���A�E�g�̕b��
	$res1 = curl_exec( $curl ) ;
	$res2 = curl_getinfo( $curl ) ;
	curl_close( $curl ) ;

	// �擾�����f�[�^
	$json = substr( $res1, $res2['header_size'] ) ;		// �擾�����f�[�^(JSON�Ȃ�)
	$header = substr( $res1, 0, $res2['header_size'] ) ;	// ���X�|���X�w�b�_�[ (���؂ɗ��p�������ꍇ�ɂǂ���)

	// [cURL]�ł͂Ȃ��A[file_get_contents()]���g���ɂ͉��L�̒ʂ�ł��c
	// $json = file_get_contents( $request_url , false , stream_context_create( $context ) ) ;

	// JSON���I�u�W�F�N�g�ɕϊ�
	$obj = json_decode( $json ) ;

	// HTML�p
	$html .= '<!DOCTYPE HTML>' ;
	$html .= '<html>' ;
	$html .= 	'<head><title>����</title></head>' ;
	$html .= 	'<body>�폜�����I' ;

	// �^�C�g��
	$html .= '<h1 style="text-align:center; border-bottom:1px solid #555; padding-bottom:12px; margin-bottom:48px; color:#D36015;">GET favorites/list</h1>' ;

	// �G���[����
	if( !$json || !$obj ) {
		$html .= '<h2>�G���[���e</h2>' ;
		$html .= '<p>�f�[�^���擾���邱�Ƃ��ł��܂���ł����c�B�ݒ���������ĉ������B</p>' ;
	}

	// ���ؗp
	$html .= '<h2>�擾�����f�[�^</h2>' ;
	$html .= '<p>���L�̃f�[�^���擾�ł��܂����B</p>' ;
	$html .= 	'<h3>�{�f�B(JSON)</h3>' ;
	$html .= 	'<p><textarea style="width:80%" rows="8">' . $json . '</textarea></p>' ;
	$html .= 	'<h3>���X�|���X�w�b�_�[</h3>' ;
	$html .= 	'<p><textarea style="width:80%" rows="8">' . $header . '</textarea></p>' ;

	// ���ؗp
	$html .= '<h2>���N�G�X�g�����f�[�^</h2>' ;
	$html .= '<p>���L���e�Ń��N�G�X�g�����܂����B</p>' ;
	$html .= 	'<h3>URL</h3>' ;
	$html .= 	'<p><textarea style="width:80%" rows="8">' . $context['http']['method'] . ' ' . $request_url . '</textarea></p>' ;
	$html .= 	'<h3>�w�b�_�[</h3>' ;
	$html .= 	'<p><textarea style="width:80%" rows="8">' . implode( "\r\n" , $context['http']['header'] ) . '</textarea></p>' ;

	// �t�b�^�[
	$html .= '<small style="display:block; border-top:1px solid #555; padding-top:12px; margin-top:72px; text-align:center; font-weight:700;">�v���O�����̐���: <a href="https://syncer.jp/Web/API/Twitter/REST_API/GET/favorites/list/" target="_blank">SYNCER</a></small>' ;
	
	// HTML�p
	$html .= '</body></html>' ;

	// �o�� (�{�ғ�����HTML�̃w�b�_�[�A�t�b�^�[��t���悤)
	echo $html ;