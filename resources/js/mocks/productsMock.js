const productsMock = {
    hero: {
        title: 'TIENDA OFICIAL',
        description:
            'Vistete con el orgullo de la provincia. Equipamiento oficial, accesorios exclusivos y ediciones especiales para vivir los colores del United dentro y fuera del estadio.',
        ctaLabel: 'Explorar catalogo',
        ctaHref: '#catalogo',
    },
    membershipBanner: {
        title: 'BENEFICIO SOCIO INDIO',
        description:
            'Obten 20% de descuento en toda la tienda y acceso a preventas exclusivas durante la temporada.',
        ctaLabel: 'UNETE AHORA',
        ctaHref: '/fanclub',
    },
    filters: ['Todos', 'Camisetas', 'Accesorios', 'Edicion especial'],
    featuredProduct: {
        id: 101,
        name: 'Camiseta Local 2024',
        category: 'Camisetas',
        badge: 'EDICION 2024',
        subtitle: 'El blanco de la provincia de Veraguas',
        price: '$65.00',
        salePrice: null,
        imageUrl:
            'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1200&q=80',
        ctaLabel: 'Agregar al carrito',
    },
    products: [
        {
            id: 102,
            name: 'Camiseta Alterna',
            category: 'Camisetas',
            badge: 'VISITANTE',
            subtitle: 'Navy & Sky: el color de la victoria',
            price: '$65.00',
            salePrice: null,
            imageUrl:
                'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1200&q=80',
            ctaLabel: 'Agregar al carrito',
        },
        {
            id: 201,
            name: 'Gorra Oficial 9Forty',
            category: 'Accesorios',
            badge: 'NUEVO',
            subtitle: 'Edicion de grada con ajuste premium',
            price: '$25.00',
            salePrice: '$20.00',
            imageUrl:
                'https://images.unsplash.com/photo-1521369909029-2afed882baee?auto=format&fit=crop&w=900&q=80',
            ctaLabel: 'Agregar al carrito',
        },
        {
            id: 202,
            name: 'Bufanda Orgullo Indio',
            category: 'Accesorios',
            badge: 'LIMITADA',
            subtitle: 'Edicion coleccionista de temporada',
            price: '$18.00',
            salePrice: null,
            imageUrl:
                'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=900&q=80',
            ctaLabel: 'Agregar al carrito',
        },
        {
            id: 203,
            name: 'Taza Black Edition',
            category: 'Accesorios',
            badge: 'EDICION ESPECIAL',
            subtitle: 'Coleccion premium para escritorio',
            price: '$12.00',
            salePrice: null,
            imageUrl:
                'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&w=900&q=80',
            ctaLabel: 'Agregar al carrito',
        },
        {
            id: 204,
            name: 'Balon Entrenamiento',
            category: 'Accesorios',
            badge: 'OFICIAL',
            subtitle: 'Listo para cancha y coleccion',
            price: '$35.00',
            salePrice: null,
            imageUrl:
                'https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=900&q=80',
            ctaLabel: 'Agregar al carrito',
        },
        {
            id: 301,
            name: 'Jersey Retro Veraguas',
            category: 'Edicion especial',
            badge: 'RETRO',
            subtitle: 'Inspirada en los origenes del club',
            price: '$75.00',
            salePrice: '$68.00',
            imageUrl:
                'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1200&q=80',
            ctaLabel: 'Agregar al carrito',
        },
    ],
};

export default productsMock;
