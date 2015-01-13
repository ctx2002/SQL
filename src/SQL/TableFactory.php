<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SQL;
use SQL\ConcreteTable;
/**
 * Description of TableFactory
 *
 * @author anru
 */
class TableFactory {
    /** Create an empty table with the specified columns.
	 *  @param name	the table name
	 *  @param columns names of all the columns
	 *  @return the table
	 */
	public static function /**Table*/ create( $name, array $columns )
	{	
            return new ConcreteTable( $name, $columns );
	}

	/** Create a table from information provided by a
	 *	{@link Table.Importer} object.
	 */
	public static function create_importer( \SQL\Importer $importer ) 
					
	{	return new ConcreteTable( $importer );
	}
        
        public static function load($name, $location = '') {
            $pattern ="%(.*)\.csv$%ism";
            if (!preg_match($pattern, $name)) {
                throw new InvalidArgumentException($name . " does not end with csv");
            }
            
            
            $in = new SplFileObject($location . $name);
            
            $loaded = new ConcreteTable( new CSVImporter( $in ));
            $in = null; //close file
            return $loaded;
        }

	/** This convenience method is equivalent to
	 *  <code>load(name, new File(".") );</code>
	 *
	 *	@see #load(String,File)
	 */
        /*
	public static Table load( String name ) throws IOException
	{	return load( name, new File(".") );
	} 
        */
	/** This convenience method is equivalent to
	 *  <code>load(name, new File(location) );</code>
	 *
	 *	@see #load(String,File)
	 */
        /*
	public static Table load( String name, String location )
												throws IOException
	{	return load( name, new File(location) );
	} */

	/* Create a table from some form stored on the disk.
	 * <p>
	 * At present, the filename extension is used to determine
	 * the data format, and only a comma-separated-value file
	 * is recognized. (The file name must end in .csv).
	 * Eventually, other extensions (like .xml) will be
	 * recognized.
	 *
	 * @param the file name. The table name is the string to the
	 * 			left of the extension. For example, if the file
	 * 			is "foo.csv," then the table name is "foo."
	 * @param the directory within which the file is found.
	 *
	 * @throws java.io.IOException if the filename extension is not
	 * 			recognized.
	 */
        /*
	public static Table load( String name, File directory )
													throws IOException
	{
		if( !(name.endsWith( ".csv" ) || name.endsWith( ".CSV" )) )
			throw new java.io.IOException(
					 "Filename (" +name+ ") does not end in "
					+"supported extension (.csv)" );

		Reader in = new FileReader( new File( directory, name ));
		Table loaded = new ConcreteTable( new CSVImporter( in ));
		in.close();
		return loaded;
	}
         * 
         */
}
