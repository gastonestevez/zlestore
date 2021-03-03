import React, { useCallback, useState } from "react";
import Warehouse from './Warehouse'

export const ProductCard = ({product, warehouses}) => {
    

    const [productStock, setProductStock] = useState(product.quantity)

    const updateStock = (value) => {
        if( (productStock-value) >= 0) {
            setProductStock(product.quantity-value)
            return value
        } else {
            return value
        }
    }
    return (
        <div key={product.id} className="boxform uk-margin">
            <legend className="uk-legend">{product.name} </legend>
            <p className="uk-text">SKU: {product.sku} </p>
            <p className="uk-text">Cantidad: {productStock} </p>
            {warehouses.map(warehouse => (
                <Warehouse warehouse={warehouse} product={product} search={updateStock} />
            ))}
        </div>
    );
};

export default ProductCard