// 2019 *********************************************************************************
var arraytruck = [
	{1: [
		["84", "Livianos ", "Accelo 815/37", "1.862.807,90", "1.303.965,53", "26.079,31", "5.476,66", "31.555,97", "15.523,40", "1.862,81", "391,19", "17.919,62", "558.842,37", "Plan 70/30"],
		["84", "Livianos  ", "Accelo 1016/37", "2.028.063,96", "1.419.644,77", "28.392,90", "5.962,51", "34.355,40", "16.900,53", "2.028,06", "425,89", "19.509,33", "608.419,19", "Plan 70/30"]
	]},
	{2: [
		["84", "Medianos", "Atego 1419/48", "2.528.590,84", "2.528.590,84", "50.571,82", "10.620,08", "61.191,90", "30.102,27", "3.612,27", "758,58", "34.748,91", "", "Plan 100%"],
		["84", "Medianos", "Atego 1419/48", "2.528.590,84", "1.770.013,59", "35.400,27", "7.434,06", "42.834,33", "21.071,59", "2.528,59", "531,00", "24.324,23", "758.577,25", "Plan 70/30"]
	]},
	{3: [
		["84", "Pesados Off Road", "Nuevo Actros 3342 S/36 6x4 Cabina M - Toma de fuerza en caja", "6.223.202,17", "6.223.202,17", "124.464,04", "26.137,45", "150.601,49", "74.085,74", "8.890,29", "1.866,96", "85.521,73", "", "Plan 100%"],
		["84", "Pesados Off Road", "Arocs 3342 K/36 Cabina M - Toma de fuerza en caja", "6.211.714,19", "6.211.714,19", "124.234,28", "26.089,20", "150.323,48", "73.948,98", "8.873,88", "1.863,51", "85.363,86", "", "Plan 100%"],
		["84", "Pesados Off Road", "Arocs 4136 B/42 8x4 Cabina M -Toma de fuerza en motor en caja", "6.627.332,85", "6.627.332,85", "132.546,66", "27.834,80", "160.381,45", "78.896,82", "9.467,62", "1.988,20", "91.075,46", "", "Plan 100%"],
		["84", "Pesados Off Road", "Arocs 4845 K/48 8x4 Cabina M - Toma de fuerza en caja", "7.166.447,27", "7.166.447,27", "143.328,95", "30.099,08", "173.428,02", "85.314,85", "10.237,78", "2.149,93", "98.484,19", "", "Plan 100%"],
		["84", "Pesados Off Road ", "Axor 3131 B/36", "5.081.407,70", "5.081.407,70", "101.628,15", "21.341,91", "122.970,07", "60.492,95", "7.259,15", "1.524,42", "69.830,74", "", "Plan 100%"],
		["84", "Pesados Off Road ", "Axor 3131 K/36", "5.006.133,99", "5.006.133,99", "100.122,68", "21.025,76", "121.148,44", "59.596,83", "7.151,62", "1.501,84", "68.796,30", "", "Plan 100%"],
		["84", "Pesados Off Road ", "Axor 3131/48 6x4 Cab Ext", "5.019.112,22", "5.019.112,22", "100.382,24", "21.080,27", "121.462,52", "59.751,34", "7.170,16", "1.505,73", "68.974,65", "", "Plan 100%"],
		["84", "Pesados Off Road ", "Axor 3131 B/36", "5.081.407,70", "3.556.985,39", "71.139,71", "14.939,34", "86.079,05", "42.345,06", "5.081,41", "1.067,10", "48.881,52", "1.524.422,31", "Plan 70/30"],
		["84", "Pesados Off Road ", "Axor 3131 K/36", "5.006.133,99", "3.504.293,79", "70.085,88", "14.718,03", "84.803,91", "41.717,78", "5.006,13", "1.051,29", "48.157,41", "1.501.840,20", "Plan 70/30"],
		["84", "Pesados Off Road ", "Axor 3131/48 6x4 Cab Ext", "5.019.112,22", "3.513.378,55", "70.267,57", "14.756,19", "85.023,76", "41.825,94", "5.019,11", "1.054,01", "48.282,25", "1.505.733,66", "Plan 70/30"],
		["84", "Pesados Off Road ", "Arocs 3342 K/36 Cabina M - Toma de fuerza en caja", "6.211.714,19", "4.348.199,93", "86.964,00", "18.262,44", "105.226,44", "51.764,28", "6.211,71", "1.304,46", "59.754,70", "1.863.514,26", "Plan 70/30"],
		["84", "Pesados Off Road ", "Arocs 4136 B/42 8x4 Cabina M -Toma de fuerza en motor en caja", "6.627.332,85", "4.639.132,99", "92.782,66", "19.484,36", "112.267,02", "55.227,77", "6.627,33", "1.391,74", "63.752,82", "1.988.199,85", "Plan 70/30"],
		["84", "Pesados Off Road ", "Arocs 4845 K/48 8x4 Cabina M - Toma de fuerza en caja", "7.166.447,27", "5.016.513,09", "100.330,26", "21.069,35", "121.399,62", "59.720,39", "7.166,45", "1.504,95", "68.938,93", "2.149.934,18", "Plan 70/30"]
	]},
	{4: [
		["84", "Pesados On Road", "Atron 1735S/45", "4.285.409,90", "4.285.409,90", "85.708,20", "17.998,72", "103.706,92", "51.016,78", "6.122,01", "1.285,62", "58.891,82", "", "Plan 100%"],
		["84", "Pesados On Road", "Atron 1735/51", "4.225.710,06", "4.225.710,06", "84.514,20", "17.747,98", "102.262,18", "50.306,07", "6.036,73", "1.267,71", "58.071,40", "", "Plan 100%"],
		["84", "Pesados On Road", "Axor 1933 S/36 CD Techo Bajo", "4.364.144,46", "4.364.144,46", "87.282,89", "18.329,41", "105.612,30", "51.954,10", "6.234,49", "1.309,24", "59.973,82", "", "Plan 100%"],
		["84", "Pesados On Road", "Axor 2036 S/36 CD Techo Elevado", "5.008.297,03", "5.008.297,03", "100.165,94", "21.034,85", "121.200,79", "59.622,58", "7.154,71", "1.502,49", "68.826,02", "", "Plan 100%"],
		["84", "Pesados On Road", "Axor 2041 S/36 CD Techo Elevado", "5.420.571,98", "5.420.571,98", "108.411,44", "22.766,40", "131.177,84", "64.530,62", "7.743,67", "1.626,17", "74.491,67", "", "Plan 100%"],
		["84", "Pesados On Road", "Actros 1841 LS/36 4x2 Cabina L Dormitorio", "5.533.049,93", "5.533.049,93", "110.661,00", "23.238,81", "133.899,81", "65.869,64", "7.904,36", "1.659,91", "76.037,39", "", "Plan 100%"],
		["84", "Pesados On Road", "Actros 2041 S/36 4x2 Cabina L Dormitorio", "5.695.277,74", "5.695.277,74", "113.905,55", "23.920,17", "137.825,72", "67.800,93", "8.136,11", "1.708,58", "78.266,78", "", "Plan 100%"],
		["84", "Pesados On Road", "Actros 2041/45 4x2 Cabina L Dormitorio", "5.412.785,04", "5.412.785,04", "108.255,70", "22.733,70", "130.989,40", "64.437,92", "7.732,55", "1.623,84", "74.384,66", "", "Plan 100%"],
		["84", "Pesados On Road", "Actros 2636 LS/33 6x2 Cabina L Dormitorio Techo Bajo (Combustible)", "5.514.880,41", "5.514.880,41", "110.297,61", "23.162,50", "133.460,11", "65.653,34", "7.878,40", "1.654,46", "75.787,69", "", "Plan 100%"],
		["84", "Pesados On Road", "Nuevo Actros 2042 LS/37 4x2", "5.694.755,15", "5.694.755,15", "113.895,10", "23.917,97", "137.813,07", "67.794,70", "8.135,36", "1.708,43", "78.259,60", "", "Plan 100%"],
		["84", "Pesados On Road", "Nuevo Actros 2042 L/46 4x2", "5.570.028,52", "5.570.028,52", "111.400,57", "23.394,12", "134.794,69", "66.309,86", "7.957,18", "1.671,01", "76.545,56", "", "Plan 100%"],
		["84", "Pesados On Road", "Nuevo Actros 2048 LS/37 4x2", "6.164.941,70", "6.164.941,70", "123.298,83", "25.892,76", "149.191,59", "73.392,16", "8.807,06", "1.849,48", "84.721,09", "", "Plan 100%"],
		["84", "Pesados On Road", "Nuevo Actros 2636 LS/33 6x2 (liviano combustible CMT 50Tn)", "5.675.061,47", "5.675.061,47", "113.501,23", "23.835,26", "137.336,49", "67.560,26", "8.107,23", "1.702,52", "77.988,96", "", "Plan 100%"],
		["84", "Pesados On Road", "Nuevo Actros 2645 LS/33 6x2 (55tN / Briten 60Tn)", "6.100.937,25", "6.100.937,25", "122.018,75", "25.623,94", "147.642,68", "72.630,21", "8.715,62", "1.830,28", "83.841,52", "", "Plan 100%"],
		["84", "Pesados On Road", "Nuevo Actros 2651 LS/40 6x4 (Bitren 75 Tn)", "6.843.963,30", "6.843.963,30", "136.879,27", "28.744,65", "165.623,91", "81.475,75", "9.777,09", "2.053,19", "94.052,48", "", "Plan 100%"],
		["84", "Pesados On Road", "Atron 1735S/45", "4.285.409,90", "2.999.786,93", "59.995,74", "12.599,11", "72.594,84", "35.711,75", "4.285,41", "899,94", "41.224,27", "1.285.622,97", "Plan 70/30"],
		["84", "Pesados On Road", "Atron 1735/51", "4.225.710,06", "2.957.997,04", "59.159,94", "12.423,59", "71.583,53", "35.214,25", "4.225,71", "887,40", "40.649,98", "1.267.713,02", "Plan 70/30"],
		["84", "Pesados On Road", "Axor 1933 S/36 CD Techo Bajo", "4.364.144,46", "3.054.901,12", "61.098,02", "12.830,58", "73.928,61", "36.367,87", "4.364,14", "916,47", "41.981,67", "1.309.243,34", "Plan 70/30"],
		["84", "Pesados On Road", "Axor 2036 S/36 CD Techo Elevado", "5.008.297,03", "3.505.807,92", "70.116,16", "14.724,39", "84.840,55", "41.735,81", "5.008,30", "1.051,74", "48.178,21", "1.502.489,11", "Plan 70/30"],
		["84", "Pesados On Road", "Axor 2041 S/36 CD Techo Elevado", "5.420.571,98", "3.794.400,38", "75.888,01", "15.936,48", "91.824,49", "45.171,43", "5.420,57", "1.138,32", "52.144,17", "1.626.171,59", "Plan 70/30"],
		["84", "Pesados On Road", "Actros 1841 LS/36 4x2 Cabina L Dormitorio", "5.533.049,93", "3.873.134,95", "77.462,70", "16.267,17", "93.729,87", "46.108,75", "5.533,05", "1.161,94", "53.226,17", "1.659.914,98", "Plan 70/30"],
		["84", "Pesados On Road", "Actros 2041 S/36 4x2 Cabina L Dormitorio", "5.695.277,74", "3.986.694,42", "79.733,89", "16.744,12", "96.478,00", "47.460,65", "5.695,28", "1.196,01", "54.786,75", "1.708.583,32", "Plan 70/30"],
		["84", "Pesados On Road", "Actros 2041/45 4x2 Cabina L Dormitorio", "5.412.785,04", "3.788.949,53", "75.778,99", "15.913,59", "91.692,58", "45.106,54", "5.412,79", "1.136,68", "52.069,26", "1.623.835,51", "Plan 70/30"],
		["84", "Pesados On Road", "Actros 2636 LS/33 6x2 Cabina L Dormitorio Techo Bajo (Combustible)", "5.514.880,41", "3.860.416,29", "77.208,33", "16.213,75", "93.422,07", "45.957,34", "5.514,88", "1.158,12", "53.051,38", "1.654.464,12", "Plan 70/30"],
		["84", "Pesados On Road", "Nuevo Actros 2042 LS/37 4x2", "5.694.755,15", "3.986.328,60", "79.726,57", "16.742,58", "96.469,15", "47.456,29", "5.694,76", "1.195,90", "54.781,72", "1.708.426,54", "Plan 70/30"],
		["84", "Pesados On Road", "Nuevo Actros 2042 L/46 4x2", "5.570.028,52", "3.899.019,97", "77.980,40", "16.375,88", "94.356,28", "46.416,90", "5.570,03", "1.169,71", "53.581,89", "1.671.008,56", "Plan 70/30"],
		["84", "Pesados On Road", "Nuevo Actros 2048 LS/37 4x2", "6.164.941,70", "4.315.459,19", "86.309,18", "18.124,93", "104.434,11", "51.374,51", "6.164,94", "1.294,64", "59.304,77", "1.849.482,51", "Plan 70/30"],
		["84", "Pesados On Road", "Nuevo Actros 2636 LS/33 6x2 (liviano combustible CMT 50Tn)", "5.675.061,47", "3.972.543,03", "79.450,86", "16.684,68", "96.135,54", "47.292,18", "5.675,06", "1.191,76", "54.592,28", "1.702.518,44", "Plan 70/30"],
		["84", "Pesados On Road", "Nuevo Actros 2645 LS/33 6x2 (55tN / Briten 60Tn)", "6.100.937,25", "4.270.656,08", "85.413,12", "17.936,76", "103.349,88", "50.841,14", "6.100,94", "1.281,20", "58.689,06", "1.830.281,18", "Plan 70/30"],
		["84", "Pesados On Road", "Nuevo Actros 2651 LS/40 6x4 (Bitren 75 Tn)", "6.843.963,30", "4.790.774,31", "95.815,49", "20.121,25", "115.936,74", "57.033,03", "6.843,96", "1.437,23", "65.836,74", "2.053.188,99", "Plan 70/30"],
		["84", "Pesados On Road", "Nuevo Actros 3342 S/36 6x4 Cabina M - Toma de fuerza en caja", "6.223.202,17", "4.356.241,52", "87.124,83", "18.296,21", "105.421,04", "51.860,02", "6.223,20", "1.306,87", "59.865,21", "1.866.960,65", "Plan 70/30"]
	]},
	{5: [
		["84", "Semipesados ", "Atego 1720/36 CN", "2.651.451,37", "2.651.451,37", "53.029,03", "11.136,10", "64.165,12", "31.564,90", "3.787,79", "795,44", "36.437,31", "", "Plan 100%"],
		["84", "Semipesados ", "Atego 1720/48 CN", "2.675.244,78", "2.675.244,78", "53.504,90", "11.236,03", "64.740,92", "31.848,15", "3.821,78", "802,57", "36.764,28", "", "Plan 100%"],
		["84", "Semipesados ", "Atego 1726 S/36 CN con ABS", "3.023.926,43", "3.023.926,43", "60.478,53", "12.700,49", "73.179,02", "35.999,12", "4.319,89", "907,18", "41.556,01", "", "Plan 100%"],
		["84", "Semipesados ", "Atego 1726 S/36 CD con ABS", "3.183.558,59", "3.183.558,59", "63.671,17", "13.370,95", "77.042,12", "37.899,51", "4.547,94", "955,07", "43.749,74", "", "Plan 100%"],
		["84", "Semipesados ", "Atego 1726/42 CN", "2.972.878,74", "2.972.878,74", "59.457,57", "12.486,09", "71.943,67", "35.391,41", "4.246,97", "891,86", "40.854,49", "", "Plan 100%"],
		["84", "Semipesados ", "Atego 1726/42 CD", "3.102.228,38", "3.102.228,38", "62.044,57", "13.029,36", "75.073,93", "36.931,29", "4.431,75", "930,67", "42.632,06", "", "Plan 100%"],
		["84", "Semipesados ", "Atego 1726/48 CN", "3.004.891,70", "3.004.891,70", "60.097,83", "12.620,55", "72.718,38", "35.772,52", "4.292,70", "901,47", "41.294,42", "", "Plan 100%"],
		["84", "Semipesados ", "Atego 2426/48", "3.472.540,40", "3.472.540,40", "69.450,81", "14.584,67", "84.035,48", "41.339,77", "4.960,77", "1.041,76", "47.721,04", "", "Plan 100%"],
		["84", "Semipesados ", "Atego 1726 A/42 4x4 Cab Ext Versión Civil •", "4.386.207,44", "4.386.207,44", "87.724,15", "18.422,07", "106.146,22", "52.216,76", "6.266,01", "1.315,86", "60.277,02", "", "Plan 100%"],
		["84", "Semipesados ", "Atego 1720/36 CN", "2.651.451,37", "1.856.015,96", "37.120,32", "7.795,27", "44.915,59", "22.095,43", "2.651,45", "556,80", "25.506,11", "795.435,41", "Plan 70/30"],
		["84", "Semipesados ", "Atego 1720/48 CN", "2.675.244,78", "1.872.671,35", "37.453,43", "7.865,22", "45.318,65", "22.293,71", "2.675,24", "561,80", "25.735,00", "802.573,43", "Plan 70/30"],
		["84", "Semipesados ", "Atego 1726 S/36 CN con ABS", "3.023.926,43", "2.116.748,50", "42.334,97", "8.890,34", "51.225,31", "25.199,39", "3.023,93", "635,02", "29.089,20", "907.177,93", "Plan 70/30"],
		["84", "Semipesados ", "Atego 1726 S/36 CD con ABS", "3.183.558,59", "2.228.491,01", "44.569,82", "9.359,66", "53.929,48", "26.529,65", "3.183,56", "668,55", "30.624,81", "955.067,58", "Plan 70/30"],
		["84", "Semipesados ", "Atego 1726/42 CN", "2.972.878,74", "2.081.015,12", "41.620,30", "8.740,26", "50.360,57", "24.773,99", "2.972,88", "624,30", "28.598,14", "891.863,62", "Plan 70/30"],
		["84", "Semipesados ", "Atego 1726/42 CD", "3.102.228,38", "2.171.559,87", "43.431,20", "9.120,55", "52.551,75", "25.851,90", "3.102,23", "651,47", "29.842,44", "930.668,51", "Plan 70/30"],
		["84", "Semipesados ", "Atego 1726/48 CN", "3.004.891,70", "2.103.424,19", "42.068,48", "8.834,38", "50.902,87", "25.040,76", "3.004,89", "631,03", "28.906,10", "901.467,51", "Plan 70/30"],
		["84", "Semipesados ", "Atego 2426/48", "3.472.540,40", "2.430.778,28", "48.615,57", "10.209,27", "58.824,83", "28.937,84", "3.472,54", "729,23", "33.404,73", "1.041.762,12", "Plan 70/30"],
		["84", "Semipesados ", "Atego 1726 A/42 4x4 Cab Ext Versión Civil •", "4.386.207,44", "3.070.345,21", "61.406,90", "12.895,45", "74.302,35", "36.551,73", "4.386,21", "921,10", "42.193,91", "1.315.862,23", "Plan 70/30"]
	]},
];
// 2019 *********************************************************************************
