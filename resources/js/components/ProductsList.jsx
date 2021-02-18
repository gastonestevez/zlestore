import React from 'react'
import ProductCard from './ProductCard'

export const ProductsList = ({products, warehouses}) => {
    return (
        <>
            {
                products.map(product => (
                    <ProductCard product={product} warehouses={warehouses} />
                    )
                )
            }
        </>
    )
}

export default ProductsList