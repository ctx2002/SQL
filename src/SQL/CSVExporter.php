<?php
namespace SQL;
use SQL\Table;
class CSVExporter implements Exporter {
    private $out;
    private $width;

    public function __construct( \SplFileObject $out )
    {	
        $this->out = $out;
    }

    public function storeMetadata(  $tableName,
                                    $width,
                                    $height,
                                    /*Iterator*/ $columnNames ) //throws IOException

    {	
        $this->width = $width;
        $this->out->fwrite($tableName == null ? "<anonymous>" : $tableName );
        $this->out->fwrite("\n");
        $this->storeRow( $columnNames ); // comma separated list of columns ids
    }

    public function storeRow( /*Iterator*/ $data ) //throws IOException
    {	
        $i = $this->width;
        /*while( data.hasNext() )
        {	Object datum = data.next();

                // Null columns are represented by an empty field
                // (two commas in a row). There's nothing to write
                // if the column data is null.
                if( datum != null )	
                        out.write( datum.toString() );

                if( --i > 0 )
                        out.write(",\t");
        }*/
        foreach ($data as $datum) {
            if( datum != null )	{
                $this->out->fwrite( $datum.toString() );  
            }
        }
        $this->out->write("\n");
    }

    public function startTable() /*throws IOException*/ { throw new \RuntimeException("nothing to do"); }
    public function  endTable()   /*throws IOException*/ { throw new \RuntimeException("nothing to do"); }
}
