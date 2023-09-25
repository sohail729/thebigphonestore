<?php


class ProductListProcessor
{
    public $objHeaders = [];
    private $uniqueCombinations = [];
    public $requiredFields = [];

    public function __construct() {
        $this->objHeaders = ['make', 'model', 'condition', 'grade', 'capacity', 'colour', 'network'];
    }

    /**
     * @param array $requiredFields
    */
    public function setRequiredFields($requiredFields){
        $this->requiredFields = $requiredFields;
    }

    public function processFile($filename)
    {

        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
        switch ($fileExtension) {
            case 'csv':
                $this->processCSV($filename);
                break;
            case 'json':
                break;
            case 'xml':
                break;
            default:
                throw new Exception("Unsupported file format: $fileExtension");
        }
       
    }

    private function displayProductObj(Product $product)
    {
        $product->setRequiredFields($this->requiredFields);
        echo json_encode($product->getProductArray(), JSON_PRETTY_PRINT);
    }
    
    public function processCSV($filename){
        if (($handle = fopen($filename, 'r')) !== false) {

            // Read the header row
            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new Exception("Empty CSV file: $filename");
            }
            
            $headers = array_combine($this->objHeaders, $headers);

            // Read the file row by row
            while (($data = fgetcsv($handle)) !== false) {
                $productData = array_combine($this->objHeaders, $data);
                $product = new Product($productData, $headers, $this->requiredFields);

                // Display product object in the terminal
                $this->displayProductObj($product);

                // Check if the similar product exists
                $this->updateUniqueCombinations($product);
            }
            fclose($handle);

            // Save unique combinations to a CSV file
            $this->saveUniqueCombinationsToFile('combination_count.csv', array_keys($headers));
        } else {
            throw new Exception("Failed to open file: $filename");
        }
    }
    private function updateUniqueCombinations(Product $product)
    {
        // Create json encoded combination key for each row
        $key = json_encode($product->toArray());
        if (!isset($this->uniqueCombinations[$key])) {  // Check if the combination exists
            $this->uniqueCombinations[$key] = 0; 
        }
        $this->uniqueCombinations[$key]++; // if exists, increase count by 1
    }

    private function saveUniqueCombinationsToFile($filename, $headers)
    {
        $file = fopen($filename, 'w');
        if ($file) {
            array_push($headers, "count");
            fputcsv($file, $headers);
            foreach ($this->uniqueCombinations as $key => $count) {
                $combination = json_decode($key, true);
                $combination['count'] = $count;
                fputcsv($file, $combination);
            }
            fclose($file);
            echo "\n\n================\n\n";
            echo "Unique combinations saved to $filename" . PHP_EOL;
        } else {
            throw new Exception("Failed to open file for writing: $filename");
        }
    }
}

?>
