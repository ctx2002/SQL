<?php
/** The Cursor provides you with a way of examining a
 *  {@link Table}, both the ones that you create and
 *  the ones that are created
 *  as a result of a select or join operation. This is
 *  an "updateable" cursor, so you can modify columns or
 *  delete rows via the cursor without problems. (Updates
 *  and deletes done through the cursor <em>are</em> handled
 *  properly with respect to the transactioning system, so
 *  they can be committed or rolled back.)
 *  <p>The class is not thread safe, however. It's a serious
 *  error for a thread to be modifying a table, either via a
 *  Cursor or directly, while another thread is examining
 *  or modifying the same table.
 *
 * <p>
 * <b>Modifications Since Publication of Holub on Patterns:</b>
 * <table border="1" cellspacing="0" cellpadding="3">
 * <tr><td valign="top">9/24/02</td>
 * 		<td>
 * 		Added a few methods to make it possible to get
 * 		column names for the JDBC
 * 		{@link java.sql.ResultSetMetaData} class.
 * 		</td>
 * </tr>
 * </table>
 *
 * @include /etc/license.txt
 */

namespace SQL;

interface Cursor
{
	/** Metadata method required by JDBC wrapper--Return the name
	 *  of the table across which we're iterating. I am deliberately
	 *  not allow access to the Table itself, because this would
	 *  allow uncontrolled modification of the table via the
	 *  iterator.
	 *  @return the name of the table or null if we're iterating
	 *  		across a nameless table like the one created by
	 *  		a select operation.
	 */
	public function tableName();

	/** Advances to the next row, or if this iterator has never
	 *  been used, advances to the first row. That is, the Cursor
	 *  is initially positioned above the first row and the
	 *  first call to <code>advance()</code> moves it to the
	 *  first row.
	 *  @throws NoSuchElementException if this call would advance
	 *  		past the last row.
	 *  @return true if the iterator is positioned at a valid
	 *  		row after the advance.
	 */
	public function advance() ;

	/** Return the number of columns in the table that we're
	 *  traversing.
	 */
	public function columnCount();

	/** Return the name of the column at the indicated index.
	 *  Note that this is a zero-referenced index---the
	 *  leftmost column is columnName(0); The JDBC
	 *  ResultSet class is 1 indexed, so don't get confused.
	 */
	public function columnName($index);

	/** Return the contents of the requested column of the current
	 *  row. You should
	 *  treat the cells accessed through this method as read only
	 *  if you ever expect to use the table in a thread-safe
	 *  environment. Modify the table using {@link Table#update}.
	 *
	 *  @throws IndexOutOfBoundsException --- the requested column
	 *  	doesn't exist.
	 */

	public function column( $columnName );

	/** Return a java.util.Iterator across all the columns in
	 *  the current row.
         *      @return Iterator
	 */
	public function columns();

	/** Return true if the iterator is traversing the
	 *  indicated table.
	 */
	public function isTraversing( Table $t );

	/** Replace the value of the indicated column of the current
	 *  row with the indicated new value.
	 *
	 *  @throws IllegalArgumentException if the newValue is
	 *  		the same as the object that's being updated.
	 *
	 *  @return the former contents of the now-modified cell.
	 */
	public function update( $columnName, $newValue );

	/** Delete the row at the current cursor position.
	 */
	public function delete();
}