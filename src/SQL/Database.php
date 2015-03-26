<?php
namespace SQL;

use SQL\Cursor;
use SQL\TableFactory;
/**
 * statement       ::=
                    INSERT  INTO IDENTIFIER [LP idList RP]
                                      VALUES LP exprList RP
                |   CREATE  DATABASE IDENTIFIER
                |   CREATE  TABLE    IDENTIFIER LP declarations RP
                |   DROP    TABLE    IDENTIFIER
                |   BEGIN    [WORK|TRAN[SACTION]]
                |   COMMIT   [WORK|TRAN[SACTION]]
                |   ROLLBACK [WORK|TRAN[SACTION]]
                |   DUMP
                |   USE     DATABASE IDENTIFIER
                |   UPDATE  IDENTIFIER SET IDENTIFIER
                                            EQUAL expr WHERE expr
                |   DELETE  FROM IDENTIFIER WHERE expr
                |   SELECT  [INTO identifier] idList
                                        FROM idList [WHERE expr]

idList          ::= IDENTIFIER idList' | STAR
idList'         ::= COMMA IDENTIFIER idList'
                |   e

declarations    ::= IDENTIFIER [type] [NOT [NULL]] declaration'
declarations'   ::= COMMA IDENTIFIER [type] declarations'
                |   COMMA PRIMARY KEY LP IDENTIFIER RP
                |   e

type            ::=  INTEGER [ LP expr RP               ]
                |    CHAR    [ LP expr RP               ]
                |    NUMERIC [ LP expr COMMA expr RP    ]
                |    DATE           // format spec is part of token

exprList        ::=       expr exprList'
exprList'       ::= COMMA expr exprList'
                |   e

expr            ::=     andExpr expr'
expr'           ::= OR  andExpr expr'
                |   e

andExpr         ::=     relationalExpr andExpr'
andExpr'        ::= AND relationalExpr andExpr'
                |   e

relationalExpr ::=          additiveExpr relationalExpr'
relationalExpr'::=    RELOP additiveExpr relationalExpr'
                    | EQUAL additiveExpr relationalExpr'
                    | LIKE  additiveExpr relationalExpr'
                    | e

additiveExpr        ::=          multiplicativeExpr additiveExpr'
additiveExpr'       ::= ADDITIVE multiplicativeExpr additiveExpr'
                    |   e

multiplicativeExpr  ::=     term multiplicativeExpr'
multiplicativeExpr' ::= STAR  term multiplicativeExpr'
                    |   SLASH term multiplicativeExpr'
                    |   e

term                ::= NOT factor
                    |   LP expr RP
                    |   factor

factor              ::= compoundId | STRING | NUMBER | NULL

compoundId          ::= IDENTIFIER compoundId'
compoundId'         ::= DOT IDENTIFIER
                    |   e
 * **/

class  RelationalOperator{ public function __construct(){}}
class  MathOperator{ public function __construct(){} }

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

    private static  $EQ;
    private static $LT;
    private static $GT;
    private static $LE;
    private static $NE;
    private static $GE;

    private static $PLUS;
    private static $MINUS;
    private static $TIMES;
    private static $DIVIDE;

    public  $tables;
    private $location;
    private $affectedRows;
    private $expression;
    private $transactionLevel = 0;
    private $in;
    /**
     * Create a database object attached to the current directory.
    *
    */
    public function __construct()
    {
        $this->tokenSet = new TokenSet();
        $this->init();
        $this->tables = array();
        $this->location = new Location(".");
    }

    protected function init()
    {
         $this->setupTokens();
         $this->setupOperator();
    }

    private function setupOperator()
    {
        self::$EQ = new RelationalOperator();
        self::$LT = new RelationalOperator();
        self::$GT = new RelationalOperator();

        self::$LE = new RelationalOperator();
        self::$GE = new RelationalOperator();
        self::$NE = new RelationalOperator();


        self::$PLUS   = new MathOperator();
        self::$MINUS  = new MathOperator();
        self::$TIMES  = new MathOperator();
        self::$DIVIDE = new MathOperator();
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
    
    public function error( /**String**/ $message )  {	
        throw $this->in->failure( $message );
    }

    /** Like {@link #error}, but throws the exception only if the
     *  test fails.
     */
    public static function verify( $test, $message ) {
    	if( !$test ) {
            throw $this->in->failure( $message );
        }
    }

    public function affectedRows()
    {
        return $this->affectedRows;
    }

    /** Use an existing "database." In the current implementation,
        *  a "database" is a directory and tables are files within
        *  the directory. An active database (opened by a constructor,
        *  a USE DATABASE directive, or a prior call to the current
        *  method) is closed and committed before the new database is
        *  opened.
        */
       public function useDatabase( $path )
       {
               $this->dump();
               $this->tables = array();
               $this->location = new Location($path);
       }

       /*******************************************************************
        *  Execute a SQL statement. If an exception is tossed and we are in the
        *  middle of a transaction (a begin has been issued but no matching
        *  commit has been seen), the transaction is rolled back.
        *
        *  @return a {@link Table} holding the result of a SELECT,
        *  	or null for statements other than SELECT.
        *  @param expression a String holding a single SQL statement. The
        *  	complete statement must be present (you cannot break a long
        *  	statement into multiple calls), and text
        *  	following the SQL statement is ignored.
        *  @throws com.holub.text.ParseFailure if the SQL is corrupt.
        *  @throws IOException Database files couldn't be accessed or created.
        *  @see #affectedRows()
        */

        public function execute( $expression )
        {
            try
             {
                $this->expression   =  $expression;
                $this->in   = new Scanner($this->tokens, $expression);
                $this->in->advance();	// advance to the first token.
                return $this->statement();
             }
             catch( ParseFailure $e )
             {
                if( transactionLevel > 0 ) {
                    rollback();
                }
                throw e;
             }
             catch( Exception $e )
             {
                if( transactionLevel > 0 ) {
                    rollback();
                }
                throw e;
             }
        }

       /** Flush to the persistent store (e.g. disk) all tables that
        *  are "dirty" (which have been modified since the database
        *  was last committed). These tables will not be flushed
        *  again unless they are modified after the current dump()
        *  call. Nothing happens if no tables are dirty.
        *  <p>
        *  The present implemenation flushes to a .csv file whose name
        *  is the table name with a ".csv" extension added.
        */
       public function dump()
       {
            /*Collection values = tables.values();
            if( values != null )
            {	for( Iterator i = values.iterator(); i.hasNext(); )
                    {	Table current = (Table ) i.next();
                            if( current.isDirty() )
                            {	Writer out =
                                            new FileWriter(
                                                            new File(location, current.name() + ".csv"));
                                    current.export( new CSVExporter(out) );
                                    out.close();
                            }
                    }
            }*/
       }

        public function createDatabase( $dirname )
        {
             $location =  new Location( $dirname );
             $location->mkdir();
             $this->location = $location;
        }

        /** Top-level expression production. Returns an Expression
	 *  object which will interpret the expression at runtime
	 *  when you call it's evaluate() method.
	 *  <PRE>
     *  expr    ::=     andExpr expr'
     *  expr'   ::= OR  andExpr expr'
     *          |   e
     *  </PRE>
	 */

        private function expr()
        {
            $left = $this->andExpr();
            while( $this->in->matchAdvance(self::$OR) != null ){
                    $left = new LogicalExpression( $left, self::$OR, $this->andExpr());
            }
            return left;
        }
        
        // andExpr			::= 	relationalExpr andExpr'
	// andExpr'			::= AND relationalExpr andExpr'
	// 					|	e

	private function andExpr()
	{	$left = $this->relationalExpr();
		while( $this->in->matchAdvance(self::$AND) != null )
			$left = new LogicalExpression( $left, self::$AND, $this->relationalExpr() );
		return $left;
	}
        
        // relationalExpr ::=   		additiveExpr relationalExpr'
	// relationalExpr'::=	  RELOP additiveExpr relationalExpr'
	// 						| EQUAL additiveExpr relationalExpr'
	// 						| LIKE  additiveExpr relationalExpr'
	// 						| e

	private function relationalExpr()
	{	$left = $this->additiveExpr();
		while( true )
		{	$lexeme;
			if( ($lexeme = $this->in->matchAdvance(self::$RELOP)) != null )
			{	/**@var RelationalOperator**/ $op;
				if( strlen($lexeme) == 1 ) {
                                    
                                    $op = $lexeme[0]=='<' ? self::$LT : self::$GT ;     
                                    
                                }
				else
                                {	
                                    //if length is not 1
                                    if (strlen($lexeme) > 1) {
                                        if ($lexeme[0] == '<' && $lexeme[1] == '>') {
                                           $op = $self::NE;
                                        } else {
                                           $op = $lexeme[0]=='<' ? self::$LE : self::$GE ;
                                        }
                                    }
                                    /*if( lexeme.charAt(0)=='<' && lexeme.charAt(1)=='>')
                                            op = NE;
                                    else
                                            op = lexeme.charAt(0)=='<' ? LE : GE ;*/
				}
				$left = new RelationalExpression($left, $op, $this->additiveExpr());
			}
			else if( $this->in->matchAdvance(self::$EQUAL) != null )
			{	$left = new RelationalExpression($left, self::$EQ, $this->additiveExpr());
			}
			else if( $this->in->matchAdvance(self::$LIKE) != null )
			{	$left = new LikeExpression($left, $this->additiveExpr());
			}
			else
				break;
		}
		return $left;
	}
        
        // additiveExpr	::= 			 multiplicativeExpr additiveExpr'
	// additiveExpr'	::= ADDITIVE multiplicativeExpr additiveExpr'
	// 					|	e

	private function /**Expression**/ additiveExpr()
	{	$lexeme;
		$left = $this->multiplicativeExpr();
		while( ($lexeme = $this->in->matchAdvance(self::$ADDITIVE)) != null )
		{	/*MathOperator*/ $op = $lexeme[0] == '+' ? self::$PLUS : self::$MINUS;
			$left = new ArithmeticExpression(
							$left, $tihs->multiplicativeExpr(), $op );
		}
		return $left;
	}

	// multiplicativeExpr	::=       term multiplicativeExpr'
	// multiplicativeExpr'	::= STAR  term multiplicativeExpr'
	// 						|	SLASH term multiplicativeExpr'
	// 						|	e

	private /*Expression*/ function multiplicativeExpr()			/*throws ParseFailure*/
	{ 
            /*Expression*/ $left = $this->term();
            while( true )
            {	if( $this->in->matchAdvance(self::$STAR) != null)
                        $left = new ArithmeticExpression( $left, $this->term(), self::$TIMES );
                    else if( $this->in->matchAdvance(self::$SLASH) != null)
                        $left = new ArithmeticExpression( $left, $this->term(), self::$DIVIDE );
                    else
                        break;
            }
            return $left;
	}

	// term				::=	NOT expr
	// 					|	LP expr RP
	// 					|	factor

	private function /*Expression*/ term()	//throws ParseFailure
	{	
            if( $this->in->matchAdvance(self::$NOT) != null )
	    {	
                return new NotExpression( $this->expr() );
	    }
		else if( $this->in->matchAdvance(self::$LP) != null )
		{	
                    /*Expression*/ $toReturn = $this->expr();
		    $this->in->required(self::$RP);
		    return $toReturn;
		}
		else
		    return $this->factor();
	}

	// factor		::= compoundId | STRING | NUMBER | NULL
	// compoundId		::= IDENTIFIER compoundId'
	// compoundId'		::= DOT IDENTIFIER
	// 					|	e

        private function factor()
        {   
            try
            {	/**@var String**/  $lexeme;
                    /**@var Value**/   $result;

                if( ($lexeme = $this->in->matchAdvance(self::$STRING)) != null ) {
                    $result = new StringValue( $lexeme );
                }
                else if( ($lexeme = $this->in->matchAdvance(self::$NUMBER)) != null ) {
                    $result = new NumericValue( $lexeme );
                }
                else if( ($lexeme = $this->in->matchAdvance(self::$NULL)) != null ) {
                    $result = new NullValue();
                }
                else
                {	$columnName  = $this->in->required(self::$IDENTIFIER);
                        $tableName   = null;

                        if( $this->in->matchAdvance(self::$DOT) != null )
                        {
                            $tableName  = $columnName;
                            $columnName = $this->in->required(self::$IDENTIFIER);
                        }

                        $result = new IdValue( $tableName, $columnName,$this );
                }
                
                return new \SQL\AtomicExpression($result);
            }
            catch( Exception $e) { /* fall through */ }

            $this->error("Couldn't parse Number"); // Always throws a ParseFailure
            return null;
        }

        //----------------------------------------------------------------------
        /*
         * declarations    ::= IDENTIFIER [type] [NOT [NULL]] declaration'
            declarations'   ::= COMMA IDENTIFIER [type] declarations'
                |   COMMA PRIMARY KEY LP IDENTIFIER RP
                |   e
         *
         *
         * type            ::=  INTEGER [ LP expr RP               ]
                |    CHAR    [ LP expr RP               ]
                |    NUMERIC [ LP expr COMMA expr RP    ]
                |    DATE           // format spec is part of token
        */
        private function declarations()
        {
            $dentifiers = array();

            $id = '';
            while( true )
            {
                if( $this->in->matchAdvance(self::$PRIMARY) != null )
                    {
                        $this->in->required(self::$KEY);
                        $this->in->required(self::$LP);
                        $this->in->required(self::$IDENTIFIER);
                        $this->in->required(self::$RP);
                    }
                    else
                    {
                        $id = $this->in->required(self::$IDENTIFIER);

                        //$identifiers->append($id);	// get the identifier
                        $identifiers[] = $id;
                            // Skip past a type declaration if one's there
                            if(	($this->in->matchAdvance(self::$INTEGER) != null)
                            ||  ($this->in->matchAdvance(self::$CHAR)    != null)	)
                            {
                                    if( $this->in->matchAdvance(self::$LP) != null )
                                    {
                                        $this->expr();
                                        $this->in->required(self::$RP);
                                    }
                            }
                            else if( $this->in->matchAdvance(self::$NUMERIC) != null )
                            {	if( $this->in->matchAdvance(self::$LP) != null )
                                    {
                                        $this->expr();
                                        $this->in->required(self::$COMMA);
                                        $this->expr();
                                        $this->in->required(self::$RP);
                                    }
                            }
                            else if( $this->in->matchAdvance(self::$DATE) 	!= null	)
                            {	 // do nothing
                            }

                            $this->in->matchAdvance( self::$NOT );
                            $this->in->matchAdvance( self::$NULL );
                    }

                    if( $this->in->matchAdvance(self::$COMMA) == null ) // no more columns
                        break;
            }

            return $identifiers;
        }

       /**
     * <PRE>
     * statement
     *      ::= CREATE  DATABASE IDENTIFIER
     *      |   CREATE  TABLE    IDENTIFIER LP idList RP
     *      |   DROP    TABLE    IDENTIFIER
     *      |   USE     DATABASE IDENTIFIER
     *      |   BEGIN    [WORK|TRAN[SACTION]]
     *      |   COMMIT   [WORK|TRAN[SACTION]]
     *      |   ROLLBACK [WORK|TRAN[SACTION]]
     *      |   DUMP
     *
     *      |   INSERT  INTO IDENTIFIER [LP idList RP]
     *                              VALUES LP exprList RP
     *      |   UPDATE  IDENTIFIER SET IDENTIFIER
     *                              EQUAL expr [WHERE expr]
     *      |   DELETE  FROM IDENTIFIER WHERE expr
     *      |   SELECT  idList [INTO table] FROM idList [WHERE expr]
     * </PRE>
	 * <p>
	 *
	 * @return a Table holding the result of a SELECT, or null for
	 *  	other SQL requests. The result table is treated like
	 *  	a normal database table if the SELECT contains an INTO
	 *  	clause, otherwise it's a temporary table that's not
	 *  	put into the database.
	 *
	 * @throws ParseFailure something's wrong with the SQL
	 * @throws IOException a database or table couldn't be opened
	 * 		or accessed.
	 * @see #createDatabase
	 * @see #createTable
	 * @see #dropTable
	 * @see #useDatabase
	 */
        private function statement()
        {
                $this->affectedRows = 0;	// is modified by UPDATE, INSERT, DELETE

                // These productions map to public method calls:

                if( $this->in->matchAdvance(self::$CREATE) != null )
                {
                    if( $this->in->match( self::$DATABASE ) )
                    {
                        $this->in->advance();
                        $this->createDatabase( $this->in->required( self::$IDENTIFIER ) );
                    }
                    else // must be CREATE TABLE
                    {	
                        //CREATE  TABLE    IDENTIFIER LP idList RP
                        $this->in->required( self::TABLE );
                        $tableName = $in->required( self::IDENTIFIER );
                        $in->required( self::$LP );
                        $this->createTable( $tableName, $this->declarations() );
                        $this->in->required( self::$RP );
                    }
                }
                else if( $this->in->matchAdvance(self::$DROP) != null )
                {	$this->in->required( self::$TABLE );
                        $this->dropTable( $this->in->required(self::$IDENTIFIER) );
                }
                else if( $this->in->matchAdvance(self::$USE) != null )
                {	$this->in->required( self::$DATABASE   );
                        $this->useDatabase( self::$IDENTIFIER );
                }

                else if( $this->in->matchAdvance(self::$BEGIN) != null )
                {	$this->in->matchAdvance(self::$WORK);	// ignore it if it's there
                        $this->begin();
                }
                else if( $this->in->matchAdvance(self::$ROLLBACK) != null )
                {	$this->in->matchAdvance(self::$WORK);	// ignore it if it's there
                        $this->rollback();
                }
                else if( $this->in->matchAdvance(self::$COMMIT) != null )
                {	$this->in->matchAdvance(self::$WORK);	// ignore it if it's there
                        $this->commit();
                }
                else if( $this->in->matchAdvance(self::$DUMP) != null )
                {	$this->dump();
                }

                // These productions must be handled via an
                // interpreter:

                else if( $this->in->matchAdvance(self::$INSERT) != null )
                {	$this->in->required( self::$INTO );
                        $tableName = $this->in->required( self::$IDENTIFIER );

                        /*List*/ $columns = null;
                        /*List*/ $values = null;

                        if( $this->in->matchAdvance(self::$LP) != null )
                        {	$columns = $this->idList();
                                $this->in->required(self::$RP);
                        }
                        if( $this->in->required(self::$VALUES) != null )
                        {	$this->in->required( self::$LP );
                                $values = $this->exprList();
                                $this->in->required( self::$RP );
                        }
                        $affectedRows = $this->doInsert( $tableName, $columns, $values );
                }
                else if( $this->in->matchAdvance(self::$UPDATE) != null )
                {	// First parse the expression
                        $tableName = $this->in->required( self::$IDENTIFIER );
                        $this->in->required( self::$SET );
                        $columnName = $this->in->required( self::$IDENTIFIER );
                        $this->in->required( self::$EQUAL );
                        /*final Expression*/ $value = $this->expr();
                        $this->in->required(self::$WHERE);
                        $affectedRows = $this->doUpdate( $tableName, $columnName, $value, $this->expr() );
                }
                else if( $this->in->matchAdvance(self::$DELETE) != null )
                {	$this->in->required( self::$FROM );
                        $tableName = $this->in->required( self::$IDENTIFIER );
                        $this->in->required( self::$WHERE );
                        $affectedRows = $this->doDelete( $tableName, $this->expr() );
                }
                else if( $this->in->matchAdvance(self::$SELECT) != null )
                {	/*List*/ $columns = $this->idList();

                        $into = null;
                        if( $this->in->matchAdvance(self::$INTO) != null )
                                $into = $this->in->required(self::$IDENTIFIER);

                        $this->in->required( self::$FROM );
                        /*List*/ $requestedTableNames = $this->idList();

                        /*Expression*/ $where = ($this->in->matchAdvance(self::$WHERE) == null)
                                                                ? null : $this->expr();
                        /*Table*/ $result = $this->doSelect($columns, $into,
                                                                $requestedTableNames, $where );
                        return $result;
                }
                else
                {	$this->error("Expected insert, create, drop, use, "
                                    . "update, delete or select");
                }

                return null;
        }
        
        /** Create a new table. If a table by this name exists, it's
	 *  overwritten.
         * 
         * $this->createTable( $tableName, $this->declarations() );
	 */
	public function createTable( /**String**/ $name, Array $columns )
        {	
            $columnNames = new SplFixedArray(count($colums) );
            $i = 0;
            foreach ($columns as $key => $value) {
                $columnNames[$i++] = (String)$value; 
            }

            $newTable = TableFactory::create($name, $columnNames);
            $this->tables[$name] = $newTable;
	}
}

class Location
{
    private $path;
    public function __construct($path) {
        $this->path = $path;
    }

    public function mkdir()
    {
        return \mkdir($this->path);
    }
    
}

//======================================================================
// The methods that parse the the productions rooted in expr work in
// concert to build an Expression object that evaluates the expression.
// This is an example of both the Interpreter and Composite pattern.
// An expression is represented in memory as an abstract syntax tree
// made up of instances of the following classes, each of which
// references its subexpressions.

interface Expression
{	/* Evaluate an expression using rows identified by the
         * two iterators passed as arguments. <code>j</code>
         * is null unless a join is being processed.
         * 
         * @return Value
         */

        public function evaluate(Cursor $tables);
}

class AtomicExpression implements Expression
{	private $atom;
        public function __construct( Value $atom )
        {	$this->atom = $atom;
        }
        public function evaluate(/* Cursor[]*/ $tables )
        {	return $atom instanceof IdValue
                        ? $this->atom->value($tables)	// lookup cell in table and
                        : $this->atom		// convert to appropriate type
                        ;
        }
}

class LogicalExpression implements Expression
{	
    private /**final boolean**/ $isAnd;
    private /**final Expression**/ $left, $right;

    public function __construct( /**Expression**/ $left, Token $op, Expression $right )
    {	
        //assert op==AND || op==OR;
        if ( !($op == self::$AND || $op==self::$OR) ) {
            throw Exception("Assertion failed. LogicalExpression");
        }
        $this->isAnd	=  ($op == self::$AND);
        $this->left	= $left;
        $this->right = $right;
    }

    public function evaluate( /***Cursor***/ $tables )
    {	
        /**@Value**/ $leftValue  = $left->evaluate($tables);
        /**@var Value**/ $rightValue = $right->evaluate($tables);
        \SQL\Databse::verify
        (	 
          $leftValue  instanceof BooleanValue
          && $rightValue instanceof BooleanValue,
          "operands to AND and OR must be logical/relational"
        );

        $l = $this->leftValue->value();
        $r = $this->rightValue->value();

        return new BooleanValue( $this->isAnd ? ($l && $r) : ($l || $r) );
    }
}

//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
class NotExpression implements Expression
{	
    private /*Expression*/ $operand;

    public function __construct( Expression $operand )
    {	
        $this->operand = $operand;
    }
    public function /*Value*/ evaluate( /*Cursor[]*/ $tables ) //throws ParseFailure
    {	
        /*Value*/ $value = $this->operand->evaluate( $tables );
        \SQL\Databse::verify( $value instanceof BooleanValue,
                              "operands to NOT must be logical/relational");
        return new BooleanValue( !$value->value() );
    }
}

class RelationalExpression implements Expression
{
    private /*RelationalOperator*/ $operator;
    private /*Expression*/ $left, $right;

    public function __construct(Expression $left,
                                RelationalOperator $operator,
                                Expression $right )
    {	
        $this->operator = $operator;
        $this->left	= $left;
        $this->right	= $right;
    }

    public function evaluate( /*Cursor[]*/ $tables ) //throws ParseFailure
    {
            $leftValue  = $this->left->evaluate ( $tables );
            $rightValue = $this->right->evaluate( $tables );

            if( 	($leftValue  instanceof StringValue)
                    ||	($rightValue instanceof StringValue) )
            {	
                \SQL\Databse::verify($this->operator==self::$EQ || $this->operator==NE,
                                            "Can't use < <= > or >= with string");

                    /*boolean*/ $isEqual =
                            $leftValue->toString() == $rightValue->toString();

                    return new BooleanValue($this->operator==self::$EQ ? $isEqual:!$isEqual);
            }

            if( $rightValue instanceof NullValue
             ||	$leftValue  instanceof NullValue )
            {
                \SQL\Databse::verify($this->operator==self::$EQ || $this->operator==self::$NE,
                                            "Can't use < <= > or >= with NULL");

                    // Return true if both the left and right sides are instances
                    // of NullValue.
                    /*boolean*/ $isEqual = 
                                    $leftValue->getClass() == $rightValue->getClass();

                    return new BooleanValue($this->operator==self::$EQ ? $isEqual : !$isEqual);
            }

            // Convert Boolean values to numbers so we can compare them.
            //
            if( $leftValue instanceof BooleanValue )
                    $leftValue = new NumericValue($leftValue->value() ? 1 : 0 );
            if( $rightValue instanceof BooleanValue )
                    $rightValue = new NumericValue($rightValue->value() ? 1 : 0 );

            \SQL\Databse::verify( 	$leftValue  instanceof NumericValue
                         && $rightValue instanceof NumericValue,
                                                             "Operands must be numbers" );

            $l = $leftValue->value();
            $r = $rightValue->value();

            return new BooleanValue
            ( 	( $this->operator == self::$EQ	  ) ? ( $l == $r ) :
                    ( $this->operator == self::$NE	  ) ? ( $l != $r ) :
                    ( $this->operator == self::$LT  	  ) ? ( $l >  $r ) :
                    ( $this->operator == self::$GT  	  ) ? ( $l <  $r ) :
                    ( $this->operator == self::$LE 	  ) ? ( $l <= $r ) :
                    /* operator == GE	 */   ( $l >= $r )
            );
    }
}
///////////////////////////////////////////////////////////////////////////////////
interface Value	// tagging interface
{
}
//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
class NullValue implements Value
{	public function toString(){ return null; }
}
//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
class BooleanValue implements Value
{	/**boolean**/ private $value;
        public function __construct( /**boolean**/ $value )
        {
            $this->value = $value;
        }
        public function value()	  { return $this->value; }
        public function toString(){ return $this->value; }
}
//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
class StringValue implements Value
{	private $value;
        public function __construct(/**String**/ $lexeme)
        {	
            //value = $lexeme.replaceAll("['\"](.*?)['\"]", "$1" );
            $this->value = preg_replace("%['\"](.*?)['\"]%", "${1}", $lexeme);
        }
        public function value()	{ return $this->value; }
        public function toString(){ return $this->value; }
}
//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
class NumericValue implements Value
{	private /***double**/ $value;
        public function __construct(/**double**/ $value)	// initialize from a double.
        {	
            if (is_numeric($value)) {
                $this->value = $value;
            } else {
                throw new Exception($value . " is not a numeric.");
            }
        }
        /*public NumericValue(String s) throws java.text.ParseException
        {	this.value = NumberFormat.getInstance().parse(s).doubleValue();
        }*/
        public function value()
        {	return $this->value;
        }
        public function toString() // round down if the fraction is very small
        {	
                /*if( Math.abs(value - Math.floor(value)) < 1.0E-20 )
                        return String.valueOf( (long)value );
                else
                        return String.valueOf( value );*/
            $this->value;
        }
}
//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
class IdValue implements Value
{	/**String**/ private $tableName;
        /**String**/ private $columnName;
            private $db;
        public function __construct(/**String**/ $tableName, $columnName,Database $database)
        {	
            $this->tableName  = $tableName;
            $this->columnName = $columnName;
            $this->db = $database;
        }

        /** Using the cursor, extract the referenced cell from
         *  the current Row and return it's contents as a String.
         *  @return the value as a String or null if the cell
         *  		was null.
         */
        public function toString(/** Cursor[]**/ $participants )
        {	$content = null;

                // If no name is to the left of the dot, then use
                // the (only) table.

                if( $this->tableName == null ) {
                    $obj = $participants[0]; 
                    $content= $obj->column( $this->columnName );
                }
                else
                {	/**Table**/ $container = /**(Table)**/ $this->db->tables['tableName'];

                        // Search for the table whose name matches
                        // the one to the left of the dot, then extract
                        // the desired column from that table.

                        $content = null;
                        for( $i = 0; $i < count( $participants ); ++$i )
                        {	if( $participants[$i]->isTraversing($container) )
                                {	$content = $participants[$i]->column($this->columnName);
                                        break;
                                }
                        }
                }

                // All table contents are converted to Strings, whatever
                // their original type. This conversion can cause
                // problems if the table was created manually.

                return ($content == null) ? null : $content . "";
        }

        /** Using the cursor, extract the referenced cell from the
         *  current row of the appropriate table, convert the
         *  contents to a {@link NullValue}, {@link NumericValue},
         *  or {@link StringValue}, as appropriate, and return
         *  that value object.
         */
        public function value( /**Cursor[]**/ $participants )
        {	$s = $this->toString( $participants );
                try
                {	return ( $s == null )
                                ? new NullValue()
                                : new NumericValue(s);
                }
                catch( Exception $e )
                {	// The NumericValue constructor failed, so it must be
                        // a string. Fall through to the return-a-string case.
                }
                return new \SQL\StringValue( $s );
        }
}



