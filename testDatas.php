<?php
/*************************************************************************************************************
 ********************************************** ANALYSE ******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'analyse'     => 'ANALYSE analyse INSERT',
	'type'        => 'ANALAYSE type INSERT',
	'test_idTest' => '1',
];

// UPDATE
$_PUT = [
	'analyse'     => 'ANALYSE analyse PUT',
	'type'        => 'ANALAYSE type PUT',
	'test_idTest' => '1',
];

/*************************************************************************************************************
 ********************************************** ARTICLE ******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'type'          => 'ARTICLE type INSERT',
	'title'         => 'ARTICLE title INSERT',
	'user_name'     => 'ARTICLE user_name INSERT',
	'date'          => '2001-01-01 01:01:01',
	'console_names' => 'PS3,PS4',
	'game_idGame'   => '1',
];

// UPDATE
$_PUT = [
	'type'          => 'ARTICLE type PUT',
	'title'         => 'ARTICLE title PUT',
	'user_name'     => 'ARTICLE user_name PUT',
	'date'          => '2002-02-02 02:02:02',
	'console_names' => 'PS3',
	'game_idGame'   => '1',
];

/*************************************************************************************************************
 ********************************************** COMMENT ******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'date'        => '2000-01-01',
	'user_name'   => 'COMMENT user_name INSERT',
	'note'        => '1',
	'like'        => '1',
	'dislike'     => '1',
	'text'        => 'COMMENT text INSERT',
	'test_idTest' => '1',
];

// UPDATE
$_PUT = [
	'date'        => '2001-02-02',
	'user_name'   => 'COMMENT user_name PUT',
	'note'        => '2',
	'like'        => '2',
	'dislike'     => '2',
	'text'        => 'COMMENT text PUT',
	'test_idTest' => '1',
];

/*************************************************************************************************************
 ********************************************** CONFIG *******************************************************
/*************************************************************************************************************/
$_POST = [
	'config'            => 'CONFIG config INSERT',
	'type'              => 'CONFIG type INSERT',
	'console_idConsole' => '1',
];

// UPDATE
$_PUT = [
	'config'            => 'CONFIG config PUT',
	'type'              => 'CONFIG type PUT',
	'console_idConsole' => '1',
];

/*************************************************************************************************************
 ******************************************** CONSOLE ********************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'game_idGame'    => '1',
	'business_model' => 'CONSOLE business_model INSERT',
	'pegi'           => 'CONSOLE pegi INSERT',
	'modes'          => [
		[
			'mode' => 'MODE mode 1 INSERT',
		],
		[
			'mode' => 'MODE mode 2 INSERT',
		],
	],
	'cover_front' => 'COVER FRONT INSERT',
	'cover_back'  => 'COVER BACK INSERT',
	'supports'    => [
		[
			'support' => 'SUPPORT support 1 INSERT',
		],
		[
			'support' => 'SUPPORT support 2 INSERT',
		],
	],
	'release'  => '2000-01-01',
	'editions' => [
		[
			'name'    => 'EDITION name 1 INSERT',
			'content' => 'EDITION content 1 INSERT',
			'shops'   => [
				[
					'url'               => 'SHOP url 1 INSERT',
					'name'              => 'SHOP name 1 INSERT',
					'price'             => '1.10',
					'devise'            => '€',
				],
				[
					'url'               => 'SHOP url 2 INSERT',
					'name'              => 'SHOP name 2 INSERT',
					'price'             => '2.20',
					'devise'            => '$',
				],
			],
		],
		[
			'name'    => 'EDITION name 2 INSERT',
			'content' => 'EDITION content 2 INSERT',
			'shops'   => [
				[
					'url'               => 'SHOP url 3 INSERT',
					'name'              => 'SHOP name 3 INSERT',
					'price'             => '3.30',
					'devise'            => '€',
				],
				[
					'url'               => 'SHOP url 4 INSERT',
					'name'              => 'SHOP name 4 INSERT',
					'price'             => '4.40',
					'devise'            => '$',
				],
			],
		],
	],
	'name'        => 'CONSOLE name INSERT',
	'description' => 'CONSOLE description INSERT',
	'dlcs'        => [
		[
			'title'             => 'DLC title 1 INSERT',
			'description'       => 'DLC description 1 INSERT',
			'price'             => '1.10',
			'devise'            => '€',
		],
		[
			'title'             => 'DLC title 2 INSERT',
			'description'       => 'DLC description 2 INSERT',
			'price'             => '2.20',
			'devise'            => '$',
		],
	],
	'configs' => [
		[
			'config'            => 'CONFIG config 1 INSERT',
			'type'              => 'CONFIG type 1 INSERT',
		],
		[
			'config'            => 'CONFIG config 2 INSERT',
			'type'              => 'CONFIG type 2 INSERT',
		],
	],
	'tests' => [
		[
			'report'            => 'TEST report INSERT',
			'date'              => '2001-01-01 00:00:00',
			'user_name'         => 'TEST user_name INSERT',
			'note'              => '1',
			'comments'          => [
				[
					'date'        => '2001-01-01 01:01:01',
					'user_name'   => 'COMMENT user_name 1 INSERT',
					'note'        => '1',
					'like'        => '1',
					'dislike'     => '1',
					'text'        => 'COMMENT text 1 INSERT',
				],
				[
					'date'        => '2002-02-02 02:02:02',
					'user_name'   => 'COMMENT user_name 2 INSERT',
					'note'        => '2',
					'like'        => '2',
					'dislike'     => '2',
					'text'        => 'COMMENT text 2 INSERT',
				],
			],
			'analyses' => [
				[
					'analyse'     => 'ANALYSE analyse 1 INSERT',
					'type'        => 'ANALYSE type 1 INSERT',
				],
				[
					'analyse'     => 'ANALYSE analyse 2 INSERT',
					'type'        => 'ANALYSE type 2 INSERT',
				],
			],
		],
	],
];

// UPDATE
$_PUT = [
	'game_idGame'    => '1',
	'business_model' => 'CONSOLE business_model PUT',
	'pegi'           => 'CONSOLE pegi PUT',
	'modes'          => [
		[
			'idMode' => '1',
		],
		[
			'mode'   => 'MODE mode 2 PUT',
		],
	],
	'cover_front' => 'COVER FRONT PUT',
	'cover_back'  => 'COVER BACK PUT',
	'supports'    => [
		[
			'idSupport' => '1',
		],
		[
			'support'   => 'SUPPORT support 2 PUT',
		],
	],
	'release'  => '2000-01-01',
	'editions' => [
		[
			'idEdition' => '1',
			'name'      => 'EDITION name 1 PUT',
			'content'   => 'EDITION content 1 PUT',
			'shops'     => [
				[
					'idShop' => '1',
					'url'    => 'SHOP url 1 PUT',
					'name'   => 'SHOP name 1 PUT',
					'price'  => '1.10',
					'devise' => '€',
				],
				[
					'idShop' => '2',
					'url'    => 'SHOP url 2 PUT',
					'name'   => 'SHOP name 2 PUT',
					'price'  => '2.20',
					'devise' => '$',
				],
			],
		],
		[
			'idEdition' => '2',
			'name'      => 'EDITION name 2 PUT',
			'content'   => 'EDITION content 2 PUT',
			'shops'     => [
				[
					'idShop' => '3',
					'url'    => 'SHOP url 3 PUT',
					'name'   => 'SHOP name 3 PUT',
					'price'  => '3.30',
					'devise' => '€',
				],
				[
					'idShop' => '4',	
					'url'    => 'SHOP url 4 PUT',
					'name'   => 'SHOP name 4 PUT',
					'price'  => '4.40',
					'devise' => '$',
				],
			],
		],
	],
	'name'        => 'CONSOLE name PUT',
	'description' => 'CONSOLE description PUT',
	'dlcs'        => [
		[
			'idDlc'			    => '1',
			'title'             => 'DLC title 1 PUT',
			'description'       => 'DLC description 1 PUT',
			'price'             => '1.10',
			'devise'            => '€',
		],
		[
			'idDlc'			    => '2',
			'title'             => 'DLC title 2 PUT',
			'description'       => 'DLC description 2 PUT',
			'price'             => '2.20',
			'devise'            => '$',
		],
	],
	'configs' => [
		[
			'idConfig'			=> '1',
			'config'            => 'CONFIG config 1 PUT',
			'type'              => 'CONFIG type 1 PUT',
		],
		[
			'idConfig'			=> '2',
			'config'            => 'CONFIG config 2 PUT',
			'type'              => 'CONFIG type 2 PUT',
		],
	],
	'tests' => [
		[
			'idTest'            => '1',
			'report'            => 'TEST report PUT',
			'date'              => '2001-01-01 00:00:00',
			'user_name'         => 'TEST user_name PUT',
			'note'              => '1',
			'comments'          => [
				[
					'idComment'   => '1',
					'date'        => '2001-01-01 01:01:01',
					'user_name'   => 'COMMENT user_name 1 PUT',
					'note'        => '1',
					'like'        => '1',
					'dislike'     => '1',
					'text'        => 'COMMENT text 1 PUT',
				],
				[
					'idComment'   => '2',
					'date'        => '2002-02-02 02:02:02',
					'user_name'   => 'COMMENT user_name 2 PUT',
					'note'        => '2',
					'like'        => '2',
					'dislike'     => '2',
					'text'        => 'COMMENT text 2 PUT',
				],
			],
			'analyses' => [
				[
					'idAnalyse'   => '1',
					'analyse'     => 'ANALYSE analyse 1 PUT',
					'type'        => 'ANALYSE type 1 PUT',
				],
				[
					'idAnalyse'   => '2',
					'analyse'     => 'ANALYSE analyse 2 PUT',
					'type'        => 'ANALYSE type 2 PUT',
				],
			],
		],
	],
];

/*************************************************************************************************************
 ************************************************** DLC ******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'title'             => 'DLC title INSERT',
	'description'       => 'DLC description INSERT',
	'price'             => '1.10',
	'devise'            => '€',
	'console_idConsole' => '1',
];

// UPDATE
$_PUT = [
	'title'             => 'DLC title PUT',
	'description'       => 'DLC description PUT',
	'price'             => '2.20',
	'devise'            => '$',
	'console_idConsole' => '1',
];

/*************************************************************************************************************
 ********************************************* EDITION *******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'name'              => 'EDITION name INSERT',
	'content'           => 'EDITION content INSERT',
	'console_idConsole' => '1',
];

// UPDATE
$_PUT = [
	'name'              => 'EDITION name PUT',
	'content'           => 'EDITION content PUT',
	'console_idConsole' => '1',
];

/*************************************************************************************************************
 ********************************************* EDITOR ********************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'editor' => 'EDITOR editor INSERT',
];

// UPDATE
$_PUT = [
	'editor' => 'EDITOR editor PUT',
];

/*************************************************************************************************************
 *********************************************** GAME ********************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'genders' => [
		[
			'gender' => 'Genre n°1',
		],
		[
			'gender' => 'Genre n°2',
		],
	],
	'title'   => 'Titre jeu n°1',
	'editors' => [
		[
			'editor' => 'Editeur n°1',
		],
		[
			'editor' => 'Editeur n°2',
		],
	],
	'themes' => [
		[
			'theme' => 'Thème n°1',
		],
		[
			'theme' => 'Thème n°2',
		],
	],
	'site'     => 'http://www.jeu-1.com/',
	'consoles' => [
		[
			'business_model'  => 'Business Model n°1',
			'pegi'            => 'Pegi n°1',
			'modes'           => [
				[
					'mode' => 'Mode n°1',
				],
				[
					'mode' => 'Mode n°2',
				],
			],
			'cover_front' => 'http://www.cover-front.com/',
			'cover_back'  => 'http://www.cover-back.com/',
			'supports'    => [
				[
					'support' => 'Support n°1',
				],
				[
					'support' => 'Support n°2',
				],
			],
			'release'  => '2015-01-25',
			'editions' => [
				[
					'name'    => 'Nom édition n°1',
					'content' => 'Contenu édition n°1',
					'shops'   => [
						[
							'url'    => 'http://www.shop-n1.com/',
							'name'   => 'Nom magasin n°1',
							'price'  => '1.10',
							'devise' => '€',
						]
					],
				],
				[
					'name'              => 'Nom édition n°2',
					'content'           => 'Contenu édition n°2',
					'console_idConsole' => '1',
					'shops'             => [
						[
							'url'    => 'http://www.shop-n2.com/',
							'name'   => 'Nom magasin n°2',
							'price'  => '2.20',
							'devise' => '$',
						]
					],
				],
			],
			'name'        => 'Nom console n°1',
			'description' => 'Description console n°1',
			'dlcs'        => [
				[
					'title'       => 'Titre DLC n°1',
					'description' => 'Description DLC n°1',
					'price'       => '1.10',
					'devise'      => '€',
				],
				[
					'title'       => 'Titre DLC n°2',
					'description' => 'Description DLC n°2',
					'price'       => '2.20',
					'devise'      => '$',
				],
			],
			'configs' => [
				[
					'config' => 'Config n°1',
					'type'   => 'Type config n°1',
				],
				[
					'config' => 'Config n°2',
					'type'   => 'Type config n°2',
				],
			],
			'tests' => [
				[
					'report'    => 'Report test n°1',
					'date'      => '2015-01-22 11:33:33',
					'user_name' => 'User name test n°1',
					'note'      => '1',
					'comments'  => [
						[
							'date'      => '2015-01-22 11:33:33',
							'user_name' => 'User name commentaire n°1',
							'note'      => '1',
							'like'      => '1',
							'dislike'   => '1',
							'text'      => 'Text commentaire n°1',
						],
						[
							'date'      => '2015-02-20 12:44:44',
							'user_name' => 'User name commentaire n°2',
							'note'      => '2',
							'like'      => '2',
							'dislike'   => '2',
							'text'      => 'Text commentaire n°2',
						],
					],
					'analyses' => [
						[
							'analyse' => 'Analyse n°1',
							'type'    => 'Type analyse n°1',
						],
						[
							'analyse' => 'Analyse n°2',
							'type'    => 'Type analyse n°2',
						],
					]
				],
			],
		],
	],
	'languages' => [
		[
			'language' => 'Langage n°1',
		],
		[
			'language' => 'Langage n°2',
		],
	],
	'articles' => [
		[
			'type'          => 'Type article n°1',
			'title'         => 'Titre article n°1',
			'user_name'     => 'User name article n°1',
			'date'          => '2015-02-05 17:54:43',
			'console_names' => 'Console names article n°1',
		],
		[
			'type'          => 'Type article n°2',
			'title'         => 'Titre article n°2',
			'user_name'     => 'User name article n°2',
			'date'          => '2015-02-05 17:54:43',
			'console_names' => 'Console names article n°2',
		],
	],
	'medias' => [
		[
			'type'          => 'Type media n°1',
			'url'           => 'URL media n°1',
			'unit'          => 'Unité media n°1',
			'width'         => '100',
			'height'        => '100',
			'console_names' => 'Console names media n°1',
		],
		[
			'type'          => 'Type media n°2',
			'url'           => 'URL media n°2',
			'unit'          => 'Unité media n°2',
			'width'         => '200',
			'height'        => '200',
			'console_names' => 'Console names media n°2',
		],
	],
	'tips' => [
		[
			'content'       => 'Contenu astuce n°1',
			'console_names' => 'Console names astuce n°1',
		],
		[
			'content'       => 'Contenu astuce n°2',
			'console_names' => 'Console names astuce n°2',
		],
	],
];

// UPDATE
$_PUT = [
	'genders' => [
		[
			'idGender' => '1',
			'gender'   => 'Genre PUT n°1',
		],
		[
			'idGender' => '2',
			'gender'   => 'Genre PUT n°2',
		],
	],
	'title'   => 'Titre jeu PUT n°1',
	'editors' => [
		[
			'idEditor' => '1',
			'editor'   => 'Editeur PUT n°1',
		],
		[
			'idEditor' => '2',
			'editor'   => 'Editeur PUT n°2',
		],
	],
	'themes' => [
		[
			'idTheme' => '1',
			'theme'   => 'Thème PUT n°1',
		],
		[
			'idTheme' => '2',
			'theme'   => 'Thème PUT n°2',
		],
	],
	'site'           => 'http://www.jeu-1-put.com/',
	'consoles'       => [
		[
			'idConsole'      => '1',
			'business_model' => 'Business Model PUT n°1',
			'pegi'           => 'Pegi PUT n°1',
			'modes'          => [
				[
					'idMode' => '1',
					'mode'   => 'Mode PUT n°1',
				],
				[
					'idMode' => '2',
					'mode'   => 'Mode PUT n°2',
				],
			],
			'cover_front' => 'http://www.cover-front.com/',
			'cover_back'  => 'http://www.cover-back.com/',
			'supports'    => [
				[
					'idSupport' => '1',
					'support'   => 'Support PUT n°1',
				],
				[
					'idSupport' => '2',
					'support'   => 'Support PUT n°2',
				],
			],
			'release'  => '2015-01-25',
			'editions' => [
				[
					'idEdition' => '1',
					'name'      => 'Nom édition PUT n°1',
					'content'   => 'Contenu édition PUT n°1',
					'shops'     => [
						[
							'idShop'    		=> '1',
							'url'               => 'http://www.shop-n1.com/',
							'name'              => 'Nom magasin PUT n°1',
							'price'             => '1.10',
							'devise'            => '€',
						],
					],
				],
				[
					'idEdition' => '2',
					'name'      => 'Nom édition PUT n°2',
					'content'   => 'Contenu édition PUT n°2',
					'shops'     => [
						[
							'idShop' => '2',
							'url'    => 'http://www.shop-n2.com/',
							'name'   => 'Nom magasin PUT n°2',
							'price'  => '2.20',
							'devise' => '$',
						],
					],
				],
			],
			'name'        => 'Nom console PUT n°1',
			'description' => 'Description console PUT n°1',
			'dlcs'        => [
				[
					'idDlc'       => '1',
					'title'       => 'Titre DLC PUT n°1',
					'description' => 'Description DLC PUT n°1',
					'price'       => '1.10',
					'devise'      => '€',
				],
				[
					'idDlc'       => '2',
					'title'       => 'Titre DLC PUT n°2',
					'description' => 'Description DLC PUT n°2',
					'price'       => '2.20',
					'devise'      => '$',
				],
			],
			'configs' => [
				[
					'idConfig' => '1',
					'config'   => 'Config PUT n°1',
					'type'     => 'Type config PUT n°1',
				],
				[
					'idConfig' => '2',
					'config'   => 'Config PUT n°2',
					'type'     => 'Type config PUT n°2',
				],
			],
			'tests' => [
				[
					'idTest'    => '1',
					'report'    => 'Report test PUT n°1',
					'date'      => '2015-01-22 11:33:33',
					'user_name' => 'User name test PUT n°1',
					'note'      => '1',
					'comments'  => [
						[
							'idComment' => '1',
							'date'      => '2015-01-22 11:33:33',
							'user_name' => 'User name commentaire PUT n°1',
							'note'      => '1',
							'like'      => '1',
							'dislike'   => '1',
							'text'      => 'Text commentaire PUT n°1',
						],
						[
							'idComment' => '2',
							'date'      => '2016-02-23 12:44:44',
							'user_name' => 'User name commentaire PUT n°2',
							'note'      => '2',
							'like'      => '2',
							'dislike'   => '2',
							'text'      => 'Text commentaire PUT n°2',
						],
					],
					'analyses' => [
						[
							'idAnalyse' => '1',
							'analyse'   => 'Analyse PUT n°1',
							'type'      => 'Type analyse PUT n°1',
						],
						[
							'idAnalyse' => '2',
							'analyse'   => 'Analyse PUT n°2',
							'type'      => 'Type analyse PUT n°2',
						],
					]
				],
			],
		],
	],
	'languages' => [
		[
			'idLanguage' => '1',
			'language'   => 'Langage PUT n°1',
		],
		[
			'idLanguage' => '2',
			'language'   => 'Langage PUT n°2',
		],
	],

	'articles' => [
		[
			'idArticle'     => '1',
			'type'          => 'Type article PUT n°1',
			'title'         => 'Titre article PUT n°1',
			'user_name'     => 'User name article PUT n°1',
			'date'          => '2015-02-05 17:54:43',
			'console_names' => 'Console names article PUT n°1',
		],
		[
			'idArticle'     => '2',
			'type'          => 'Type article PUT n°2',
			'title'         => 'Titre article PUT n°2',
			'user_name'     => 'User name article PUT n°2',
			'date'          => '2015-02-05 17:54:43',
			'console_names' => 'Console names article PUT n°2',
		],
	],

	'medias' => [
		[
			'idMedia'     => '1',
			'type'          => 'Type media PUT n°1',
			'url'           => 'URL media PUT n°1',
			'unit'          => 'Unité media PUT n°1',
			'width'         => '110',
			'height'        => '110',
			'console_names' => 'Console names media PUT n°1',
		],
		[
			'idMedia'       => '2',
			'type'          => 'Type media PUT n°2',
			'url'           => 'URL media PUT n°2',
			'unit'          => 'Unité media PUT n°2',
			'width'         => '220',
			'height'        => '220',
			'console_names' => 'Console names media PUT n°2',
		],
	],

	'tips' => [
		[
			'idTip'         => '1',
			'content'       => 'Contenu astuce PUT n°1',
			'console_names' => 'Console names astuce PUT n°1',
		],
		[
			'idTip'         => '2',
			'content'       => 'Contenu astuce PUT n°2',
			'console_names' => 'Console names astuce PUT n°2',
		],
	],
];

/*************************************************************************************************************
 ********************************************** GENDER *******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'gender' => 'GENDER gender INSERT',
];

// UPDATE
$_PUT = [
	'gender' => 'GENDER gender PUT',
];

/*************************************************************************************************************
 ******************************************** LANGUAGE *******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'language' => 'LANGUAGE language INSERT',
];

// UPDATE
$_PUT = [
	'language' => 'LANGUAGE language PUT',
];

/*************************************************************************************************************
 *********************************************** MEDIA *******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'type'          => 'MEDIA type INSERT',
	'url'           => 'MEDIA url INSERT',
	'unit'          => 'px',
	'width'         => '1.10',
	'height'        => '1.10',
	'console_names' => 'PS3,PS4',
	'game_idGame'   => '1',
];

// UPDATE
$_PUT = [
	'type'          => 'MEDIA type PUT',
	'url'           => 'MEDIA url PUT',
	'unit'          => 'cm',
	'width'         => '2.20',
	'height'        => '2.20',
	'console_names' => 'PS3',
	'game_idGame'   => '1',
];

/*************************************************************************************************************
 ******************************************** MODE *******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'mode' => 'MODE mode INSERT',
];

// UPDATE
$_PUT = [
	'mode' => 'MODE mode PUT',
];

/*************************************************************************************************************
 ******************************************** ROLE *******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'role' => 'ROLE role INSERT',
];

// UPDATE
$_PUT = [
	'role' => 'ROLE role PUT',
];

/*************************************************************************************************************
 *********************************************** SHOP ********************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'url'               => 'SHOP url INSERT',
	'name'              => 'SHOP name INSERT',
	'price'             => '1.10',
	'devise'            => '€',
	'edition_idEdition' => '1',
];

// UPDATE
$_PUT = [
	'url'               => 'SHOP url PUT',
	'name'              => 'SHOP name PUT',
	'price'             => '1.10',
	'devise'            => '€',
	'edition_idEdition' => '1',
];

/*************************************************************************************************************
 ********************************************** SUPPORT ******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'support' => 'SUPPORT support INSERT',
];

// UPDATE
$_PUT = [
	'support' => 'SUPPORT support PUT',
];

/*************************************************************************************************************
 ********************************************** TEST ******************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'report'            => 'TEST report INSERT',
	'date'              => '2001-01-01 01:01:01',
	'user_name'         => 'TEST user_name INSERT',
	'note'              => '1',
	'console_idConsole' => '1',
];

// UPDATE
$_PUT = [
	'report'            => 'TEST report PUT',
	'date'              => '2002-02-02 02:02:02',
	'user_name'         => 'TEST user_name PUT',
	'note'              => '2',
	'console_idConsole' => '1',
];

/*************************************************************************************************************
 ********************************************** THEME ********************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'theme' => 'THEME theme INSERT',
];

// UPDATE
$_PUT = [
	'theme' => 'THEME theme PUT',
];

/*************************************************************************************************************
 ************************************************ TIP ********************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'tip' => 'TIP tip INSERT',
];

// UPDATE
$_PUT = [
	'tip' => 'TIP tip PUT',
];

/*************************************************************************************************************
 ********************************************** USER *********************************************************
/*************************************************************************************************************/
// INSERT
$_POST = [
	'email'    => 'USER email INSERT',
	'username' => 'USER username INSERT',
	'password' => 'USER password INSERT',
	'role'     => '1',
];

// UPDATE
$_PUT = [
	'email'    => 'USER email PUT',
	'username' => 'USER username PUT',
	'password' => 'USER password PUT',
	'role'     => '2',
];

