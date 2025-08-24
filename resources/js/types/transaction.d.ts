import { Customer } from './customer';

export interface Transaction {
    id: number;
    customer_id: number;
    invoice_number: string;
    invoice_date: string;
    total: number;
    customer?: Customer;
    detail_transactions?: DetailTransaction[];
    created_at?: string;
    updated_at?: string;
}

export interface DetailTransaction {
    id: number;
    transaction_id: number;
    product_id: number;
    invoice_number: string;
    product_code: string;
    product_name: string;
    quantity: number;
    price_at_time: number;
    disc1: number;
    disc2: number;
    disc3: number;
    net_price: number;
    amount: number;
    created_at?: string;
    updated_at?: string;
}

export interface TransactionFormData {
    customer_id: number | string;
    invoice_date: string;
    total: number;
    items: TransactionItem[];
}

export interface TransactionItem {
    product_id: number;
    quantity: number;
    price_at_time: number;
    disc1: number;
    disc2: number;
    disc3: number;
    net_price: number;
    amount: number;
}

export interface CustomerDetails {
    id: number;
    customer_code: string;
    name: string;
    address: string;
}

export interface ProductDetails {
    id: number;
    product_code: string;
    name: string;
    price: number;
    stock: number;
}
