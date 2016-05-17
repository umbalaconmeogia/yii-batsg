<?php
/**
 * This function need PHPExcel (http://phpexcel.codeplex.com/) library.
 */
class MySqlSchemaFromExcelCommand extends CConsoleCommand
{
  /**
   * Create schema from schema.xlsx in the running directory.
   */
  public function actionCreateSchema()
  {
    $lines = $this->loadExcelData('./schema.xlsx');

    CsvColumn::setIndexToColumnName($lines[0]);

    $tables = array();
    $table = NULL;
    for ($i = 1; $i < count($lines); $i++) {
      $data = $lines[$i];
      if ($data[0] == '*') {
        if ($data[1] == 'tableName') {
          $table = new TableDef($data);
          $tables[] = $table;
        } else if ($data[1] == 'constraint') {
          $table->addConstraint($data[2]);
        } else {
          throw new Exception("Unsupport {$data[1]} type.");
        }
      } else {
        $columnDef = new ColumnDef($data, $table);
        $table->addColumnDef($columnDef);
      }
    }

    // Create sql.
    $drop = '';
    $create = '';
    foreach ($tables as $table) {
      $drop = $table->sqlDrop() . "\n$drop";
      $create .= "$table\n\n";
    }
    file_put_contents('schema.mysql.sql', "SET NAMES 'utf8';\n\n$drop\n$create");


    // Create html.
    file_put_contents('schema.mysql.html', TableDef::generateHtml($tables));

  }

  /**
   * @param string $excelFilePath
   * @return string[][]
   */
  private function loadExcelData($excelFilePath)
  {
    $sheet = HPhpExcel::openExcel($excelFilePath);
    $rowNum = $sheet->getHighestRow();
    Yii::trace("Excel row num: $rowNum");

    $data = array();
    for ($rowIndex = 1; $rowIndex <= $rowNum; $rowIndex++) {
      $line = array();
      for ($colIndex = 0; $colIndex < 8; $colIndex++) {
        $line[] = HPhpExcel::getCell($sheet, $rowIndex, $colIndex);
      }
      $data[] = $line;
    }
    return $data;
  }
}
?>
<?php
class TableDef
{
  const FOREIGN_KEY_PATTERN = '/FOREIGN KEY \((\w+)\) REFERENCES (\w+)\((\w+)\)/';

  public $name;
  private $constraints;
  private $columnDefs;

  // column -> table
  private $foreignKeys = array();

  /**
   * @param TableDef[] $tables
   * @return string
   */
  public static function generateHtml($tables)
  {
    $createDate = date('Y-m-d H:i');
    $tocHtml = '';
    $tableHtml = '';
    foreach ($tables as $i => $table) {
      $index = $i + 1;
      $tocHtml .= "<tr><td>$index</td><td>{$table->name}</td><td>{$table->comment1}</td></tr>";
      $tableHtml .= $table->toHtml();
    }
    $tocHtml = "<table>$tocHtml</table>";
    $tableHtml = "<table>$tableHtml</table>";

    $html = <<<EOT
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-language" content="ja" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
    table {
      border-collapse: collapse;
    }
    td {
      border: 1px solid black;
    }
    .table {
      background-color: rgb(146, 208, 80);
      font-weight: bold;
      page-break-inside: auto;
      page-break-before: always;
    }
    .columnHeader {
      background-color: rgb(216, 228, 188);
    }
    .row0 {
      background-color: rgb(238, 236, 225);
    }
    .row1 {
      background-color: rgb(221, 217, 196);
    }
    .default-column {
      background-color: rgb(252, 213, 180);
    }
    tr:hover {
      background-color: #FFFFDD !important;
    }
  </style>
  <title>データベーステーブル仕様書</title>
</head>
<body>
<p>Create date: $createDate</p>
<h2>テーブル一覧</h2>
$tocHtml

$tableHtml
</body>
</html>
EOT;
    return $html;
  }

  public function __construct($data)
  {
    foreach ($data as $index => $value) {
      $column = CsvColumn::$indexToColumnName[$index];
      $this->$column = trim($value);
    }
    $this->name = $data[2];

    $this->constraints = array();
    $this->columnDefs = array();
  }

  public function addColumnDef($columnDef)
  {
    $this->columnDefs[] = $columnDef;
  }

  public function addConstraint($constraint)
  {
    $this->constraints[] = $constraint;

    if (preg_match(self::FOREIGN_KEY_PATTERN, $constraint, $matches)) {
      $this->foreignKeys[$matches[1]] = "{$matches[2]}.{$matches[3]}";
    }
  }

  public function sqlDrop()
  {
    return "DROP TABLE IF EXISTS {$this->name};";
  }

  public function __toString()
  {
    $tableDef = array();

    $columnDefs = array();
    foreach ($this->columnDefs as $columnDef) {
      $columnDefs[] = "  $columnDef";
    }
    $tableDef[] = join(",\n", $columnDefs);

    $constraints = array();
    foreach ($this->constraints as $constraint) {
      $constraints[] = "  $constraint";
    }
    if ($constraints) {
      $tableDef[] = join(",\n", $constraints);
    }

    $s = "CREATE TABLE {$this->name} (\n";
    $s .= join(",\n", $tableDef);
    $s .= "\n) COMMENT '{$this->comment1}'\nENGINE = INNODB;";

    return $s;
  }

  public function toHtml()
  {
  /*
    $s = '<tr><td><br /></td></tr>';
    $s .= '<tr class="table">';
    $s .= '<td>テーブル</td>';
    $s .= "<td>{$this->comment1}</td>";
    $s .= "<td>{$this->name}</td>";
    $s .= '</tr>';
  */
    $s = '<br />';
    $s .= "<div>{$this->comment1}</div>";
    $s .= "<div>テーブルID：{$this->name}</div>";
    $s .= '<table>';
    $s .= ColumnDef::htmlHeader('columnHeader');
    foreach ($this->columnDefs as $i => $columnDef) {
      if (in_array($columnDef->name, array('id', 'data_status', 'create_time', 'update_time'))) {
        $cssClass = 'default-column';
      } else {
        $cssClass = 'row' . ($i % 2);
      }
      $cssClass = 'row' . ($i % 2);
      $refTo = isset($this->foreignKeys[$columnDef->name]) ? $this->foreignKeys[$columnDef->name] : NULL;
      $s .= "\n" . $columnDef->toHtml($i + 1, $cssClass, $refTo);
    }
    $s .= '</table>';

    return $s;
  }
}

class ColumnDef
{
  private $tableDef;

  public $name;
  private $type;
  private $size;
  private $comment;

  private $notNull;
  private $default;

  // Additional info, such as PRIMARY KEY AUTO INCREMENT
  private $other;

  /**
   * @param string[] $data Array contains data fields' values.
   */
  public function __construct($data, $tableDef)
  {
    foreach ($data as $index => $value) {
      $column = CsvColumn::$indexToColumnName[$index];
      $this->$column = trim($value);
    }

    $this->tableDef = $tableDef;
  }

  public function __toString()
  {
    // Generate data type.
    $type = $this->sqlType();

    // Generate NOT NULL
    $notNull = $this->notNull ? ' NOT NULL' : NULL;

    // Generate DEFAULT
    $default = $this->default !== '' ? " DEFAULT {$this->default}" : NULL;

    $other = $this->other;
    if ($this->name == 'id') {
      $other = 'PRIMARY KEY AUTO_INCREMENT';
    }
    // Generate other (PRIMARY KEY etc)
    $other = $other ? " {$other}" : NULL;

    // Generate comment.
    $comment = $this->comment1 ? "{$this->comment}\t{$this->comment1}" : $this->comment;
    $comment = $comment ? " COMMENT '{$comment}'" : NULL;

    $s = "{$this->name} $type$notNull$default$other$comment";

    return $s;
  }

  /**
   * Generate TYPE(SIZE)
   * @return string
   */
  public function sqlType()
  {
    $type = $this->type;
    if ($this->size) {
      $type .= "({$this->size})";
    }
    return $type;
  }

  public static function htmlHeader($cssClass = '')
  {
    $s = "<tr class=\"$cssClass\">";
    $s .= "<td>No.</td>";
    $s .= "<td>コラム名</td>";
    $s .= "<td>コラム</td>";
    $s .= "<td>タイプ</td>";
    $s .= "<td>NULL</td>";
    $s .= "<td>既定値</td>";
//    $s .= "<td>その他</td>";
    $s .= "<td>コメント</td>";
    $s .= "<td>参照</td>";
    $s .= '</tr>';
    return $s;
  }

  public function toHtml($no, $cssClass = '', $refTo = NULL)
  {
    $s = "<tr class=\"$cssClass\">";
    $s .= "<td>$no</td>";
    $s .= "<td>{$this->comment}</td>";
    $s .= "<td><a name=\"{$this->tableDef->name}.{$this->name}\"></a>{$this->name}</td>";
    $s .= "<td>{$this->sqlType()}</td>";
    $s .= "<td>{$this->notNull}</td>";
    $s .= "<td>{$this->default}</td>";
//    $s .= "<td>{$this->other}</td>";
    $s .= "<td>{$this->comment1}</td>";

    if ($refTo) {
      $refTo = '<a href="#' . $refTo . '">' . $refTo . '</a>';
    }
    $s .= "<td>$refTo</td>";

    $s .= '</tr>';
    return $s;
  }
}

class CsvColumn
{
  /**
   * @var string[]
   */
  public static $indexToColumnName;

  /**
   * @param string[] $indexToColumnName
   */
  public static function setIndexToColumnName($indexToColumnName)
  {
    self::$indexToColumnName = $indexToColumnName;
  }
}
?>