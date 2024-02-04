export default {
    data() {
        return {
            flightType: [
                {
                    value: 0,
                    label: 'None'
                },
                {
                    value: 1,
                    label: 'One Way Flight'
                },
                {
                    value: 2,
                    label: 'Return Flight'
                }
            ],
            mainOfficeList: [
                {value: 0, label: 'Kuala Lumpur'},
                {value: 1, label: 'Miri'},
                {value: 2, label: 'Bintulu'},
            ],
            travelPurpose: [
                {value: 'null', label: 'All'},
                {value: true, label: 'Corporate'},
                {value: false, label: 'Project'},
            ],
            allowanceType: [
                {value: 'Meal Allowance', label: 'Meal Allowance', allowance: 25.00},
                {value: 'Outstation', label: 'Outstation', allowance: 70.00},
                {value: 'Oversea (Executive)', label: 'Oversea (Executive)', allowance: 200.00},
                {value: 'Oversea (Management)', label: 'Oversea (Management)', allowance: 250.00},
                {value: 'Oversea (Sr.Management)', label: 'Oversea (Sr.Management)', allowance: 350.00},
                {value: 'Offshore (Category 1)', label: 'Offshore (Category 1)', allowance: 150.00},
                {value: 'Offshore (Category 2)', label: 'Offshore (Category 2)', allowance: 100.00},
                {value: 'Others', label: 'Others', allowance: null}
            ],
            travelFrom: [
                {
                    value: 'KL OFFICE(VSQ)',
                    label: 'KL OFFICE(VSQ)'
                },
                {
                    value: 'KSB YARD/TELUK KALONG',
                    label: 'KSB YARD/TELUK KALONG'
                },
                {
                    value: 'Others',
                    label: 'Others',
                    distance: null
                }
            ],
            travelToKL: [
                {
                    value: 'CITY BANK (TALISMAN)',
                    label: 'CITY BANK (TALISMAN)',
                    distance: 15
                },
                {
                    value: 'SAPURA',
                    label: 'SAPURA',
                    distance: 18
                },
                {
                    value: 'KLCC-PETRONAS',
                    label: 'KLCC-PETRONAS',
                    distance: 15
                },
                {
                    value: 'CHUKAI KEMAMAN',
                    label: 'CHUKAI KEMAMAN',
                    distance: 310
                },
                {
                    value: 'KSB KEMAMAN/TELUK KALONG',
                    label: 'KSB KEMAMAN/TELUK KALONG',
                    distance: 320
                },
                {
                    value: 'KOP KERTEH',
                    label: 'KOP KERTEH',
                    distance: 350
                },
                {
                    value: 'TCOT KERTEH',
                    label: 'TCOT KERTEH',
                    distance: 350
                },
                {
                    value: 'NIOSH BANGI',
                    label: 'NIOSH BANGI',
                    distance: 35
                },
                {
                    value: 'SEQU KAYU ARA',
                    label: 'SEQU KAYU ARA',
                    distance: 16
                },
                {
                    value: 'PUTRAJAYA DOSH',
                    label: 'PUTRAJAYA DOSH',
                    distance: 35
                },
                {
                    value: 'AIRPORT KLIA',
                    label: 'AIRPORT KLIA',
                    distance: 60
                },
                {
                    value: 'AIRPORT KLIA2',
                    label: 'AIRPORT KLIA2',
                    distance: 62
                },
                {
                    value: 'AIRPORT SUBANG',
                    label: 'AIRPORT SUBANG',
                    distance: 20
                },
                {
                    value: 'AIRPORT KERTEH',
                    label: 'AIRPORT KERTEH',
                    distance: 360
                },
                {
                    value: 'PCSB PMO',
                    label: 'PCSB PMO',
                    distance: 335
                },
                {
                    value: 'DUNGUN',
                    label: 'DUNGUN',
                    distance: 70
                },
                {
                    value: 'TOWER 3',
                    label: 'TOWER 3',
                    distance: 16
                },
                {
                    value: 'KL SENTRAL',
                    label: 'KL SENTRAL',
                    distance: 13
                },
                {
                    value: 'Others',
                    label: 'Others',
                    distance: null
                }
            ],
            travelToKSB: [
                {
                    value: 'CHUKAI KEMAMAN',
                    label: 'CHUKAI KEMAMAN',
                    distance: 10
                },
                {
                    value: 'TCOT (TERENGGANU CRUDE OIL TERMINAL- KERTEH)',
                    label: 'TCOT (TERENGGANU CRUDE OIL TERMINAL- KERTEH)',
                    distance: 45
                },
                {
                    value: 'KOP (KOMPLEKS OPERASI PETRONAS-KERTEH)',
                    label: 'KOP (KOMPLEKS OPERASI PETRONAS-KERTEH)',
                    distance: 30
                },
                {
                    value: 'AIRPORT KERTEH',
                    label: 'AIRPORT KERTEH',
                    distance: 40
                },

                {
                    value: 'AIRPORT KUANTAN',
                    label: 'AIRPORT KUANTAN',
                    distance: 85
                },
                {
                    value: 'PCSB PMO',
                    label: 'PCSB PMO',
                    distance: 25
                },
                {
                    value: 'OGT (ONSHORE GAS TERMINAL)',
                    label: 'OGT (ONSHORE GAS TERMINAL)',
                    distance: 45
                },
                {
                    value: 'PETROSYTEM GEBENG',
                    label: 'PETROSYTEM GEBENG',
                    distance: 50
                },
                {
                    value: 'OSC (ONSHORE SLUGCATCHER)',
                    label: 'OSC (ONSHORE SLUGCATCHER)',
                    distance: 50
                },
                {
                    value: 'AIRPORT KT',
                    label: 'AIRPORT KT',
                    distance: 172
                },
                {
                    value: 'AIRPORT KLIA',
                    label: 'AIRPORT KLIA',
                    distance: 380
                },
                {
                    value: 'AIRPORT KLIA2',
                    label: 'AIRPORT KLIA2',
                    distance: 388
                },
                {
                    value: 'PMO',
                    label: 'PMO',
                    distance: 40
                },
                {
                    value: 'PULAU DUYUNG',
                    label: 'PULAU DUYUNG',
                    distance: 175
                },
                {
                    value: 'TGAST-TRG GAS TERMINAL',
                    label: 'TGAST-TRG GAS TERMINAL',
                    distance: 45
                },
                {
                    value: 'Others',
                    label: 'Others',
                    distance: null
                }
            ],
            transportOption: [
                {
                    value: 'Mileage',
                    label: 'Mileage'
                },
                {
                    value: 'Parking',
                    label: 'Parking',
                },
                {
                    value: 'Toll',
                    label: 'Toll',
                },

                {
                    value: 'Public Transport',
                    label: 'Public Transport',
                },
                {
                    value: 'Fuel',
                    label: 'Fuel',
                },
            ],
            expenseOption: [
                {
                    value: 'Accommodation',
                    label: 'Accommodation',
                },
                {
                    value: 'Refreshment',
                    label: 'Refreshment',
                },
                {
                    value: 'Telephone',
                    label: 'Telephone',
                },
                {
                    value: 'Laundry',
                    label: 'Laundry',
                },
                {
                    value: 'Others',
                    label: 'Others',
                }
            ]
        };
    },
};
