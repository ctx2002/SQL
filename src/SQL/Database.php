<?php
namespace SQL;

use SQL\Cursor;
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

        private $tables;
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
                //return statement();
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
            $dentifiers = new ArrayObject();

            $id;
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

                        $identifiers->append($id);	// get the identifier

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

                            $this->in->matchAdvance( self::NOT );
                            $this->in->matchAdvance( self::NULL );
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
                        {	$this->in->required( self::TABLE );
                                $tableName = $in->required( self::IDENTIFIER );
                                $in->required( self::$LP );
                                $this->createTable( $tableName, $this->declarations() );
                                $this->in->required( self::$RP );
                        }
                }
                else if( in.matchAdvance(DROP) != null )
                {	in.required( TABLE );
                        dropTable( in.required(IDENTIFIER) );
                }
                else if( in.matchAdvance(USE) != null )
                {	in.required( DATABASE   );
                        useDatabase( new File( in.required(IDENTIFIER) ));
                }

                else if( in.matchAdvance(BEGIN) != null )
                {	in.matchAdvance(WORK);	// ignore it if it's there
                        begin();
                }
                else if( in.matchAdvance(ROLLBACK) != null )
                {	in.matchAdvance(WORK);	// ignore it if it's there
                        rollback();
                }
                else if( in.matchAdvance(COMMIT) != null )
                {	in.matchAdvance(WORK);	// ignore it if it's there
                        commit();
                }
                else if( in.matchAdvance(DUMP) != null )
                {	dump();
                }

                // These productions must be handled via an
                // interpreter:

                else if( in.matchAdvance(INSERT) != null )
                {	in.required( INTO );
                        String tableName = in.required( IDENTIFIER );

                        List columns = null, values = null;

                        if( in.matchAdvance(LP) != null )
                        {	columns = idList();
                                in.required(RP);
                        }
                        if( in.required(VALUES) != null )
                        {	in.required( LP );
                                values = exprList();
                                in.required( RP );
                        }
                        affectedRows = doInsert( tableName, columns, values );
                }
                else if( in.matchAdvance(UPDATE) != null )
                {	// First parse the expression
                        String tableName = in.required( IDENTIFIER );
                        in.required( SET );
                        final String columnName = in.required( IDENTIFIER );
                        in.required( EQUAL );
                        final Expression value = expr();
                        in.required(WHERE);
                        affectedRows =
                                doUpdate( tableName, columnName, value, expr() );
                }
                else if( in.matchAdvance(DELETE) != null )
                {	in.required( FROM );
                        String tableName = in.required( IDENTIFIER );
                        in.required( WHERE );
                        affectedRows = doDelete( tableName, expr() );
                }
                else if( in.matchAdvance(SELECT) != null )
                {	List columns = idList();

                        String into = null;
                        if( in.matchAdvance(INTO) != null )
                                into = in.required(IDENTIFIER);

                        in.required( FROM );
                        List requestedTableNames = idList();

                        Expression where = (in.matchAdvance(WHERE) == null)
                                                                ? null : expr();
                        Table result = doSelect(columns, into,
                                                                requestedTableNames, where );
                        return result;
                }
                else
                {	error("Expected insert, create, drop, use, "
                                                                                +"update, delete or select");
                }

                return null;
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
         */

        public function evaluate(Cursor $tables);
}


