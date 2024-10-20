<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ProductController extends ResourceController
{
    protected $modelName = "App\Models\ProductModel";
    protected $format = "json";

    //post - title, cost, description, image
    public function addProduct(){
        $validationRules = [
            "title" => [
                "rules" => "required|min_length[5]",
                "errors" => [
                    "required" => "The title field is required.",
                    "min_length" => "Title must be at least 5 characters long."
                ]
            ],
            "cost" => [
                "rules" => "required|integer|greater_than[0]",
                "errors" => [
                    "required" => "The cost field is required.",
                    "integer" => "Cost must be an integer.",
                    "greater_than" => "Cost must be greater than 0."
                ]
            ]
        ];
    
        // Validation check
        if(!$this->validate($validationRules)){
            return $this->fail($this->validator->getErrors());
        }
    
        // Handling the image upload
        $imageFile = $this->request->getFile("product_image");
        $productImageURL = null;
        if($imageFile && $imageFile->isValid()){
            $newProductImageName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . "uploads", $newProductImageName);
            $productImageURL = "uploads/" . $newProductImageName;
        }
    
        // Retrieve form data
        $data = $this->request->getPost();
        $title = $data['title'];
        $cost = $data['cost'];
        $description = isset($data['description']) ? $data['description'] : "";
    
        // Insert into the database
        if($this->model->insert([
            "title" => $title,
            "cost" => $cost,
            "description" => $description,
            "product_image" => $productImageURL
        ])){
            return $this->respond([
                "status" => true,
                "message" => "Product added successfully",
            ]);
        } else {
            return $this->respond([
                "status" => false,
                "message" => "Failed to add product",
            ]);
        }
    }

    //get
    public function listAllProducts(){
        $products = $this->model->findAll();
        return $this->respond([
            "status" => true,
            "message" => "Products found",
            "products" => $products
        ]);
    }

    //get - id

    public function getSingleProduct($product_id){

        $product = $this->model->find($product_id);
        if($product){
        return $this->respond([
            "status" => true,
            "message" => "Product found",
            "product" => $product
        ]);
    }else{
        return $this->respond([
            "status" => false,
            "message" => "Product not found"

        ]);
    }
    }

    //put - id, title, cost, description, image

    public function updateProduct($product_id){

        $product = $this->model->find($product_id);
        if($product){

            $updated_data = json_decode(file_get_contents("php://input"), true);

            $product_title = isset($updated_data['title']) ? $updated_data['title'] : $product['title'];
            $product_cost = isset($updated_data['cost']) ? $updated_data['cost'] : $product['cost'];
            $product_description = isset($updated_data['description'])? $updated_data['description'] : $product['description'];

            $productImageObject = $this->request->getFile("product_image");
            $productImageURL = $product['product_image'];

            if($productImageObject){

                $newProductImagename = $productImageObject->getRandomName();
                $productImageObject->move(FCPATH . "uploads", $newProductImageName);

                $productImageURL = "uploads/" . $productImageName;


            }

            if($this->model->update($product_id, [
                "title" => $product_title,
                "cost" => $product_cost,
                "description" => $product_description,
                "product_image" => $productImageURL
            ])){
                return $this->respond([
                    "status" => true,
                    "message" => "Product updated successfully"
                ]);
            } else  {
                return $this->respond([
                    "status" => false,
                    "message" => "Failed to update product"
                ]);
            }
        }else{
            return $this->respond([
                "status" => false,
                "message" => "Product not found"
            ]);
        }

    }
    //delete
    public function deleteProduct($product_id){
        $product = $this->model->find($product_id);

        if($product){

        if($this->model->delete($product_id)){
            return $this->respond([
                        "status" => true,
                        "message" => "Product deleted successfully"
                    ]);
                    } else {
                        return $this->respond([
                            "status" => false,
                            "message" => "Failed to delete product"
                        ]);
                    }

        }else{

            return $this->respond([
                            "status" => false,
                            "message" => "Product not found"
                        ]);
        }

    }
}
