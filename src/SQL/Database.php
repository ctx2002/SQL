<?php
namespace SQL;

class Database {
    private $tokenSet;
    private static $COMMA;
    private static $EQUAL;

    private static $LP;
    private static $RP;
        private static $DOT ;
        private static $STAR  ;
        private static $SLASH ;
        private static $AND;
        private static $BEGIN;
        private static $COMMIT;
        private static $CREATE;
        private static $DATABASE ;
        private static $DELETE;
        private static $DROP;
        private static $DUMP;
        private static $FROM;
        private static $INSERT;
        private static $INTO;
        private static $KEY;
        private static $LIKE;
        private static $NOT;
        private static $NULL;
        private static $OR;
        private static $PRIMARY;
        private static $ROLLBACK	;
        private static $SELECT;
        private static $SET;
        private static $TABLE;
        private static $UPDATE;
        private static $USE;
        private static $VALUES;
        private static $WHERE;
        private static $WORK;
        private static $ADDITIVE;
        private static $STRING;
        private static $RELOP;
        private static $NUMBER;
        private static $INTEGER;
        private static $NUMERIC;
        private static $CHAR;
        private static $DATE;
        private static $IDENTIFIER;//{=Database.lastToken}

    public function __construct()
    {
        $this->tokenSet = new TokenSet();
    }

    protected function init()
    {

    }

    private function setupTokens()
    {
        self::$COMMA =  $this->tokenSet->create( "'," 		); //{=Database.firstToken}
        self::$EQUAL =   $this->tokenSet->create("'="                               );
        self::$LP       =    $this->tokenSet->create( "'(" 		);
        self::$RP       =    $this->tokenSet->create( "')" 		);
        self::$DOT     =    $this->tokenSet->create( "'." 		);
        self::$STAR     =    $this->tokenSet->create( "'*" 		);
        self::$SLASH    =    $this->tokenSet->create( "'/" 		);
        self::$AND	=   $this->tokenSet->create( "'AND"		);
        self::$BEGIN	=   $this->tokenSet->create( "'BEGIN"		);
        self::$COMMIT	=   $this->tokenSet->create( "'COMMIT"		);
        self::$CREATE	=   $this->tokenSet->create( "'CREATE"		);
        self::$DATABASE =   $this->tokenSet->create( "'DATABASE"		);
        self::$DELETE	=   $this->tokenSet->create( "'DELETE"		);
        self::$DROP	=   $this->tokenSet->create( "'DROP"		);
        self::$DUMP	=   $this->tokenSet->create( "'DUMP"		);
        self::$FROM	=   $this->tokenSet->create( "'FROM"		);
        self::$INSERT	=   $this->tokenSet->create( "'INSERT"		);
        self::$INTO	=   $this->tokenSet->create( "'INTO"		);
        self::$KEY	=   $this->tokenSet->create( "'KEY"		);
        self::$LIKE	=   $this->tokenSet->create( "'LIKE"		);
        self::$NOT	=   $this->tokenSet->create( "'NOT"		);
        self::$NULL	=   $this->tokenSet->create( "'NULL"		);
        self::$OR	=   $this->tokenSet->create( "'OR"		);
        self::$PRIMARY	=   $this->tokenSet->create( "'PRIMARY"		);
        self::$ROLLBACK	=   $this->tokenSet->create( "'ROLLBACK"		);
        self::$SELECT	=   $this->tokenSet->create( "'SELECT"		);
        self::$SET	=   $this->tokenSet->create( "'SET"		);
        self::$TABLE	=   $this->tokenSet->create( "'TABLE"		);
        self::$UPDATE	=   $this->tokenSet->create( "'UPDATE"		);
        self::$USE	=   $this->tokenSet->create( "'USE"		);
        self::$VALUES	=   $this->tokenSet->create( "'VALUES"		);
        self::$WHERE	=   $this->tokenSet->create( "'WHERE"		);
        self::$WORK	=   $this->tokenSet->create( "'WORK"		);
        self::$ADDITIVE	=   $this->tokenSet->create( "\\+|-"		);
        self::$STRING	=   $this->tokenSet->create( "(\".*?\")|('.*?')"		);
        self::$RELOP	=   $this->tokenSet->create( "[<>][=>]?"		);
        self::$NUMBER	=   $this->tokenSet->create( "(small|tiny|big)?int(eger)?"	);
        self::$INTEGER	=   $this->tokenSet->create( "[0-9]+(\\.[0-9]+)?"		);
        self::$NUMERIC	=   $this->tokenSet->create( "decimal|numeric|real|double"	);
        self::$CHAR	=   $this->tokenSet->create( "(var)?char"		);
        self::$DATE	=   $this->tokenSet->create( "date(\\s*\\(.*?\\))?"		);
        self::$IDENTIFIER	=   $this->tokenSet->create( "[a-zA-Z_0-9/\\\\:~]+"	);//{=Database.lastToken}

    }
}
