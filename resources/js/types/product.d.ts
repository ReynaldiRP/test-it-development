export interface Product {
    id: number;
    product_code: string;
    name: string;
    price: number;
    stock: number;
    created_at?: string;
    updated_at?: string;
}

export interface ProductFormData {
    product_code: string;
    name: string;
    price: number | string;
    stock: number | string;
}
