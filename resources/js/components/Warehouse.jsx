import React, { useState, useCallback } from 'react'

export const Warehouse = ({warehouse, product, search}) => {
    const [stock, setStock] = useState(0)

    
    const searchProductStock = useCallback( (whProducts, productId) => {
        const search = whProducts.find( whp => {
            return productId == whp.woo_id
        })
        return search.stock.quantity
    }, [product])
    
    const searchStock = () => (searchProductStock(
        warehouse.get_products,
        product.product_id
    ))
    
    const handleOnChange = (e) => {
        const { value } = e.currentTarget
        setStock(search(value))
    }

    return (
        <div
            key={`${product.id}-p-${warehouse.id}-w`}
            className="uk-margin"
        >
            <label htmlFor="stock" className="uk-form-label">
                Depósito: {warehouse.name} @ {warehouse.address} /
                Disponibles:{" "}
                {searchStock()}
            </label>
            <div
                key={`${product.id}-${warehouse.id}-increment`}
                className="uk-form-controls"
            >
                <input
                    type="number"
                    className="uk-input uk-form-width-small"
                    min="0"
                    value={stock}
                    onChange={handleOnChange}
                />
            </div>
        </div>
    )
}

export default Warehouse