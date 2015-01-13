<?php

namespace SQL;

/***
 *	Pass this importer to a {@link Table} constructor (such
 *	as
 *	{link com.holub.database.ConcreteTable#ConcreteTable(Table.Importer)}
 *	to initialize
 *	a <code>Table</code> from
 *	a comma-sparated-value repressentation. For example:
 *	<PRE>
 *	Reader in = new FileReader( "people.csv" );
 *	people = new ConcreteTable( new CSVImporter(in) );
 *	in.close();
 *	</PRE>
 *	The input file for a table called "name" with
 *	columns "first," "last," and "addrId" would look
 *	like this:
 *	<PRE>
 *	name
 *	first,	last,	addrId
 *	Fred,	Flintstone,	1
 *	Wilma,	Flintstone,	1
 *	Allen,	Holub,	0
 *	</PRE>
 *	The first line is the table name, the second line
 *	identifies the columns, and the subsequent lines define
 *	the rows.
 *
 * @include /etc/license.txt
 *
 * @see Table
 * @see \SQL\Table
 * @see CSVExporter
 */



class CSVImporter implements \SQL\Importer
{	
    private /**BufferedReader**/ $in;			// null once end-of-file reached
    private /**String[]**/      $columnNames;
    private /**String**/        $tableName;

    public function __construct( /**Reader**/ SplFileObject $in )
    {	
        $this->in = $in;
    }
    public function startTable()
    {	
        $this->tableName   = trim( $in.fgets());
        //$columnNames = in.readLine().split("\\s*,\\s*");
        $this->columnNames = split(",",$in.fgets());
    }
    public function loadTableName()
    {	return $this->tableName;
    }
    public function loadWidth()
    {	return count( $this->columnNames);
    }
    /**
     * @return ArrayIterator
     * **/
    public function loadColumnNames()
    {	return new ArrayIterator($this->columnNames);  //{=CSVImporter.ArrayIteratorCall}
    }

    public function loadRow()
    {	
        //Iterator row = null;
        $row = null;
        if( $this->in != null )
        {   
            if ( $in->eof() ) {
                $this->in = null;
            } else {
                $line = $in->fgets();
                $row = new ArrayIterator( preg_split("%\s*,\s*%",$line));
            }
            /*
            if( $line == null )
                in = null;
            else
                    row = new ArrayIterator( line.split("\\s*,\\s*"));
             * */
             
        }
        return $row;
    }

    public function endTable() {}
}

