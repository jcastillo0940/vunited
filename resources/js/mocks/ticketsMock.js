const ticketsMock = {
    match: {
        competition: 'JORNADA 12 - LPF',
        dateLabel: '24 DE OCTUBRE, 2026',
        timeLabel: '20:00 PM',
        stadium: 'ESTADIO ATALAYA, VERAGUAS',
        homeTeam: 'VERAGUAS UNITED',
        awayTeam: 'CAI PANAMA',
        homeLogoLabel: 'VUFC',
        awayLogoLabel: 'CAI',
    },
    zones: [
        {
            id: 'general',
            name: 'General',
            displayName: 'GENERAL',
            area: 'SUR / NORTE',
            price: 5,
            description: 'Acceso a las graderias generales laterales.',
            tone: 'neutral',
        },
        {
            id: 'preferencial',
            name: 'Preferencial',
            displayName: 'PREFERENCIAL',
            area: 'ESTE / OESTE',
            price: 12,
            description: 'Asientos numerados con mejor visibilidad.',
            tone: 'neutral',
        },
        {
            id: 'vip',
            name: 'VIP Indio',
            displayName: 'VIP INDIO',
            area: 'PREMIUM',
            price: 25,
            description: 'Zona exclusiva con servicio de catering.',
            tone: 'featured',
        },
    ],
    quantityLimit: 6,
    successTicket: {
        title: 'PAGO EXITOSO',
        subtitle:
            'Tu transaccion visual ha sido completada. Este boleto es una representacion mock sin validacion real.',
        ticketType: 'BOLETO DIGITAL OFICIAL',
        gate: 'PUERTA A3',
        seatLabel: 'ZONA PREMIUM',
        qrLabel: 'QR PLACEHOLDER',
    },
};

export default ticketsMock;
