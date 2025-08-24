// Indonesia package types
export interface Province {
    id: number;
    code: string;
    name: string;
    meta?: string;
    created_at?: string;
    updated_at?: string;
}

export interface City {
    id: number;
    code: string;
    province_code: string;
    name: string;
    meta?: string;
    created_at?: string;
    updated_at?: string;
}

export interface District {
    id: number;
    code: string;
    city_code: string;
    name: string;
    meta?: string;
    created_at?: string;
    updated_at?: string;
}

export interface Village {
    id: number;
    code: string;
    district_code: string;
    name: string;
    meta?: string;
    created_at?: string;
    updated_at?: string;
}

export interface Location {
    id?: number;
    address: string;
    province: string;
    cities: string;
    district: string;
    sub_district: string;
    postal_code: string;
}

export interface Customer {
    id: number;
    customer_code: string;
    name: string;
    location: Location;
}

export interface CustomerFormData {
    code: string;
    name: string;
    location: {
        address: string;
        province: string;
        cities: string;
        district: string;
        subdistrict: string;
        postal_code: string;
    };
}
