import React, { useCallback } from "react";
import Warehouse from './Warehouse'

export const ProductCard = ({product, warehouses}) => {
    const searchProductStock = useCallback( (whProducts, productId) => {
        const search = whProducts.find( whp => {
            return productId == whp.woo_id
        })
        return search.stock.quantity
    }, [product])

    return (
        <div key={product.id} className="boxform uk-margin">
            <legend className="uk-legend">{product.name} </legend>
            <p className="uk-text">SKU: {product.sku} </p>
            <p className="uk-text">Cantidad: {product.quantity} </p>
            {warehouses.map(warehouse => (
                <Warehouse warehouse={warehouse} product={product} search={searchProductStock} />
            ))}
        </div>
    );
};

export default ProductCard