const cartMock = {
    hero: {
        title: 'CARRITO DE COMPRAS',
        description:
            'Revisa tus articulos del United, ajusta cantidades y prepara tu orden de tienda. El carrito sigue siendo local, pero el pago se redirige a PayPal.',
    },
    items: [
        {
            id: 101,
            name: 'Camiseta Local Oficial',
            variant: 'Talla L | Temporada 2024',
            price: 65,
            quantity: 1,
            imageUrl:
                'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80',
        },
        {
            id: 204,
            name: 'Balon de Entrenamiento',
            variant: 'Calidad Pro | Talla 5',
            price: 35,
            quantity: 1,
            imageUrl:
                'https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=900&q=80',
        },
    ],
    shipping: 5,
    validCoupon: {
        code: 'TRIBU10',
        amount: 10,
    },
    securityNotice: {
        title: 'Pago seguro',
        description:
            'El pago de tienda se realiza en PayPal. No capturamos tarjeta ni CVV en este sitio y los cupones siguen siendo visuales por ahora.',
    },
};

export default cartMock;
