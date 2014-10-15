<?php

namespace SQL;

class ScannerTest extends \PHPUnit_Framework_TestCase {

    public function testMatch() {
        /*
         * private static final Token
          COMMA		= tokens.create( "'," 			 	),
          IN			= tokens.create( "'IN'" 		 	),
          INPUT		= tokens.create( "INPUT"		 	),
          IDENTIFIER	= tokens.create( "[a-z_][a-z_0-9]*" );
         * * */
        $set = new TokenSet();
        $comma = $set->create("',");
        $in = $set->create("'IN");
        $input = $set->create("INPUT");
        $id = $set->create("[a-z_][a-z_0-9]*");
        $analyzer = new Scanner($set, ",aBc In input inputted");

        $token = $analyzer->advance();
        $this->assertEquals($comma->__toString(), $token->__toString());
       
        //analyzer->advance() == COMMA
        $token = $analyzer->advance();
        $this->assertEquals($id->__toString(), $token->__toString());
        
        $token = $analyzer->advance();
        $this->assertEquals($in->__toString(), $token->__toString());
        
        $token = $analyzer->advance();
        $this->assertEquals($input->__toString(), $token->__toString());
        
        $token = $analyzer->advance();
        $this->assertEquals($id->__toString(), $token->__toString());
        
        $analyzer = new Scanner($set, "Abc IN\nCde");
	$analyzer->advance(); // advance to first token.
        
        /***
         * assert( analyzer.matchAdvance(IDENTIFIER).equals("Abc") );
			assert( analyzer.matchAdvance(IN).equals("in")  );
			assert( analyzer.matchAdvance(IDENTIFIER).equals("Cde") );
         * ***/
        $this->assertEquals($analyzer->matchAdvance($id),"Abc");
        $rt = $analyzer->matchAdvance($in);
       // $this->assertEquals($rt,"in");
        $this->assertEquals($analyzer->matchAdvance($id),"Cde");
       
        $analyzer = new Scanner($set, "xyz\nabc + def");
        $analyzer->advance();
        $analyzer->advance();
       
        $this->setExpectedException('Exception');
        $analyzer->advance();
        
    }

}
