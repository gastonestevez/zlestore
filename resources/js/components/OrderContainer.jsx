import React, { useEffect } from "react";
import { Provider, useDispatch, useSelector } from "react-redux";
import store from "../redux/store";
import ReactDOM from "react-dom";
import { fetchOrderById, fetchWarehouses } from "../redux/thunk/orderThunk";
import { OrderSummary } from "./OrderSummary"
import { ProductsList } from './ProductsList'

const OrderContainer = ({ orderId }) => {
    const dispatch = useDispatch();
    const order = useSelector(({ orderReducer }) => orderReducer.order)
    const warehouses = useSelector(({ orderReducer }) => orderReducer.warehouses)
    useEffect(() => {
        dispatch(fetchOrderById(orderId))
        dispatch(fetchWarehouses())
    }, [dispatch]);

    return (
        <>
            <div className="uk-container primer-div uk-margin">
                <h1 className="uk-heading-divider">Preparar pedido</h1>
                {order && warehouses? (
                    <>
                        <OrderSummary order={order} />
                        <ProductsList products={order.line_items} warehouses={warehouses} />
                    </>
                ) : (
                    <span
                        className="uk-margin-small-right"
                        uk-spinner="ratio: 1"
                    ></span>
                )}
            </div>
        </>
    );
};

export default OrderContainer;

if (document.getElementById("orderContainer")) {
    const orderId = document.getElementById("orderId");
    ReactDOM.render(
        <Provider store={store}>
            <OrderContainer orderId={orderId.value} />
        </Provider>,
        document.getElementById("orderContainer")
    );
}
