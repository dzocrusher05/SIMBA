-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2025 at 09:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simba_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `aset`
--

CREATE TABLE `aset` (
  `id` int(11) NOT NULL,
  `no_bmn` varchar(100) NOT NULL,
  `nama_bmn` varchar(255) NOT NULL,
  `status` enum('Tersedia','Dipinjam') NOT NULL DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aset`
--

INSERT INTO `aset` (`id`, `no_bmn`, `nama_bmn`, `status`) VALUES
(82, '30201040011', 'Sepeda Motor', 'Tersedia'),
(83, '30201050181', 'Mobil Unit Kesehatan Masyarakat', 'Tersedia'),
(84, '30301020021', 'Mesin Bor Tangan', 'Tersedia'),
(85, '30501040261', 'Koper/Tas', 'Tersedia'),
(86, '30501040262', 'Koper/Tas', 'Tersedia'),
(87, '30501050481', 'LCD Sony', 'Tersedia'),
(88, '30501050482', 'LCD Acer X1', 'Tersedia'),
(89, '30501050483', 'LCD Acer X1', 'Tersedia'),
(90, '30502060081', 'Sound System Yamaha', 'Tersedia'),
(91, '30502060082', 'Sound System Huper', 'Tersedia'),
(92, '30502060201', 'Webcam Philips', 'Tersedia'),
(93, '30502060202', 'Webcam Philips', 'Tersedia'),
(94, '30502060203', 'Camera Teslong NTS ', 'Tersedia'),
(95, '30502060341', 'Tangga Alumunium', 'Tersedia'),
(96, '30601010361', 'Microphone X-One', 'Tersedia'),
(97, '30601010881', 'Voice Recorder SONY ICD-UX570F', 'Tersedia'),
(98, '30601010882', 'Voice Recorder SONY ICD-UX570F', 'Tersedia'),
(99, '30601010981', 'Mixer Sound Sistem', 'Tersedia'),
(100, '30601021281', 'Camera Digital Canon', 'Tersedia'),
(101, '30601021282', 'Camera Digital SONY ILCE 6000L', 'Tersedia'),
(102, '30601021283', 'Camera Digital Sony RX IV', 'Tersedia'),
(103, '30601021671', 'Drone DJI Mavic 3', 'Tersedia'),
(104, '30601050371', 'Teropong/Keker', 'Tersedia'),
(105, '31001020011', 'P.C Lenovo', 'Tersedia'),
(106, '31001020012', 'P.C Lenovo', 'Tersedia'),
(107, '31001020013', 'P.C Lenovo', 'Tersedia'),
(108, '31001020014', 'P.C Lenovo HP ALL IN ONE 22 C0051D', 'Tersedia'),
(109, '31001020015', 'P.C Lenovo All in One A340-221CK', 'Tersedia'),
(110, '31001020016', 'P.C Lenovo All in One A340-221CK', 'Tersedia'),
(111, '31001020017', 'P.C Lenovo All in One A340-221CK', 'Tersedia'),
(112, '31001020018', 'P.C Lenovo All in One A340-221CK', 'Tersedia'),
(113, '31001020019', 'P.C DELL / OPTIPLEX 7020', 'Tersedia'),
(114, '31002030031', 'Printer EPSON L5190 ALL IN ONE', 'Tersedia'),
(115, '31002030032', 'Printer HP OFFICEJET 7110', 'Tersedia'),
(116, '31002030033', 'Printer HP OFFICEJET 7110', 'Tersedia'),
(117, '31002030034', 'Printer EPSON', 'Tersedia'),
(118, '31002030035', 'Printer EPSON M200', 'Tersedia'),
(119, '31002030036', 'Printer EPSON WF-100', 'Tersedia'),
(120, '31002030037', 'Printer EPSON WF-100', 'Tersedia'),
(121, '31002030038', 'Printer EPSON L565', 'Tersedia'),
(122, '31002030039', 'Printer HP OFFICEJET AIO 7730', 'Tersedia'),
(123, '310020300310', 'Printer EPSON INKJET COLOR MFPL617', 'Tersedia'),
(124, '310020300311', 'Printer EPSON L5190 ALL IN ONE', 'Tersedia'),
(125, '310020300312', 'Printer Canon G4010', 'Tersedia'),
(126, '310020300313', 'Printer Canon G4010', 'Tersedia'),
(127, '310020300314', 'Printer HPRT MT800Q', 'Tersedia'),
(128, '310020300315', 'Printer L3210', 'Tersedia'),
(129, '310020300316', 'Printer L3210', 'Tersedia'),
(130, '310020300317', 'Printer L3210', 'Tersedia'),
(131, '310020300318', 'Printer L3210', 'Tersedia'),
(133, '30502069991', 'Mobil Terios', 'Tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id` int(11) NOT NULL,
  `peminjaman_id` int(11) NOT NULL,
  `aset_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_permintaan`
--

CREATE TABLE `detail_permintaan` (
  `id` int(11) NOT NULL,
  `permintaan_id` int(11) NOT NULL,
  `persediaan_id` int(11) NOT NULL,
  `jumlah_diminta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nomor`
--

CREATE TABLE `nomor` (
  `sbbk` int(11) NOT NULL DEFAULT 0,
  `spb` int(11) NOT NULL DEFAULT 0,
  `peminjaman` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `nama_peminjam` varchar(255) NOT NULL,
  `nomor_telepon_peminjam` varchar(20) NOT NULL,
  `alasan_peminjaman` text NOT NULL,
  `lokasi_peminjaman` varchar(255) NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status_peminjaman` enum('Diajukan','Disetujui','Ditolak','Dikembalikan') NOT NULL DEFAULT 'Diajukan',
  `tanda_tangan_peminjam` longtext DEFAULT NULL,
  `tanda_tangan_admin` longtext DEFAULT NULL,
  `nomor_surat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `nama_pengaturan` varchar(100) NOT NULL,
  `nilai_pengaturan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permintaan_persediaan`
--

CREATE TABLE `permintaan_persediaan` (
  `id` int(11) NOT NULL,
  `nama_pemohon` varchar(255) NOT NULL,
  `nomor_telepon_pemohon` varchar(20) NOT NULL,
  `tanggal_permintaan` date NOT NULL,
  `status_permintaan` enum('Diajukan','Disetujui','Ditolak') NOT NULL DEFAULT 'Diajukan',
  `nomor_spb` varchar(50) DEFAULT NULL,
  `nomor_sbbk` varchar(50) DEFAULT NULL,
  `tanda_tangan_pemohon` text DEFAULT NULL,
  `tanda_tangan_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persediaan`
--

CREATE TABLE `persediaan` (
  `id` int(11) NOT NULL,
  `nama_persediaan` varchar(255) NOT NULL,
  `stok` int(11) NOT NULL,
  `satuan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `persediaan`
--

INSERT INTO `persediaan` (`id`, `nama_persediaan`, `stok`, `satuan`) VALUES
(82, 'COPPER SULFATE PENTAHYDRATE FOR ANAL', 0, 'PCS'),
(83, 'POTASSIUM IODIDE 500GR', 0, 'BOTOL'),
(84, 'SODIUM HYDROXIDE 1 KG', 1, 'BOTOL'),
(85, 'NINHYDRIN 10 GR', 1, 'BOTOL'),
(86, 'Methanyl Yellow Test Kit (Tidak Digunakan)', 0, 'pkt'),
(87, 'Rhodamin B Test Kit (Tidak Digunakan)', 0, 'Pkt'),
(88, 'Boraks Test Kit (Tidak Diguankan)', 0, 'Pkt'),
(89, 'Formalin Test Kit (Tidak Digunakan)', 0, 'Pkt'),
(90, 'Kadar Iodium Test Kit', 0, 'Pkt'),
(91, 'DNA Porcine', 0, 'Pkt'),
(92, 'Food Security Test Kit', 0, 'Pkt'),
(93, 'Arsenic Test Kit', 0, 'Pkt'),
(94, 'Timbal Test Kit', 0, 'Pkt'),
(95, 'Nitrite Test Kit', 0, 'Pkt'),
(96, 'Cyanide Test Kit', 1, 'Pkt'),
(97, 'Test Kit Formalin_CHEM KIT', 1, 'Box'),
(98, 'Test Kit Boraks_CHEM KIT', 1, 'Box'),
(99, 'Test Kit Rhodamin B_CHEM KIT', 0, 'Box'),
(100, 'Test Kit Methanil Yellow_CHEM KIT', 1, 'Box'),
(101, 'Test Cyanida_SUPELCO (TIDAK DIGUNAKAN)', 0, 'Box'),
(102, 'LEAD TEST KIT', 1, 'Pkt'),
(103, 'ETHANOL', 0, 'Botol'),
(104, 'HYDROCHLORIC', 0, 'Botol'),
(105, 'CHLOROFORM', 0, 'Botol'),
(106, 'N-HEXANE', 1, 'Botol'),
(107, 'ACETONITRILE FOR HPLC', 2, 'BOTOL'),
(108, 'SULPHURIC ACID 98%', 1, 'BOTOL'),
(109, 'ACETONE', 1, 'BOTOL'),
(110, 'N-BUTANOL, PROPYLCARBINOL', 0, 'BOTOL'),
(111, 'DIETHYLETHER', 1, 'BOTOL'),
(112, 'DIKLOROMETAN', 0, 'BOTOL'),
(113, 'ETIL ASETAT', 0, 'BOTOL'),
(114, 'FORMALDEHYDE SOLUTION 37%', 0, 'BOTOL'),
(115, 'METHANOL', 1, 'BOTOL'),
(116, 'NINHYDRIN 10GR', 0, 'BOTOL'),
(117, 'CYCLOHEXANE', 1, 'BOTOL'),
(118, 'BUFFER PH 4', 1, 'BOTOL'),
(119, 'BUFFER PH 7', 1, 'BOTOL'),
(120, 'BUFFER PH 9', 1, 'BOTOL'),
(121, 'POTASSIUM DICHROMATE 500GR', 0, 'BOTOL'),
(122, 'SODIUM NITRITE 1KG', 0, 'BOTOL'),
(123, 'TOLUEN', 1, 'BOTOL'),
(124, 'ALKOHOL', 1, 'BOTOL'),
(125, 'SPIRITUS', 0, 'BOTOL'),
(126, 'ALUMINIUM FOIL KECIL', 0, 'ROLL'),
(127, 'NaCl', 0, 'BOTOL'),
(128, 'Amoksisilin Trihidrat (BPFI B 0414017)', 0, 'Vial'),
(129, 'Deksametason (BPFI B 0314048', 0, 'vial'),
(130, 'Ibuprofen (BPFI B 0114263)', 0, 'vial'),
(131, 'Diltiazem Hidroklorida (BPFI B0218129)', 0, 'vial'),
(132, 'Tiamfenikol (BPFI B 0216325)', 0, 'vial'),
(133, 'Domperidon Maleat (BPFI B 0116383', 0, 'vial'),
(134, 'Asam Mefenamat (BPFI B0321131)', 0, 'vial'),
(135, 'Haloperidol (BPFI)', 0, 'vial'),
(136, 'Prednison (BPFI AB0321457)', 1, 'vial'),
(137, 'Baku Klebsiella aerogenes ATCC 13048', 0, 'tabung'),
(138, 'Baku Escherichia coli ATCC 8739', 0, 'Tabung'),
(139, 'Baku Escherichia coli WDCM 00013', 0, 'vial'),
(140, 'Baku Candida albicans WDCM 00054', 0, 'vial'),
(141, 'Baku Saccharomyces cerevisiae NCPF 3191', 0, 'vial'),
(142, 'POTATO DEXTROSE AGAR', 0, 'PAKET'),
(143, 'MAXIMUM RECOVERY DILUENT', 0, 'PAKET'),
(144, 'CHLORAMPHENICOL', 0, 'PAKET'),
(145, 'PLATE COUNT AGAR', 0, 'PAKET'),
(146, 'TRIPHENYLTETRAZOLIUM CHLORIDE', 0, 'PAKET'),
(147, 'LAURYL SULFATE BROTH', 0, 'PAKET'),
(148, 'EC BROTH', 0, 'PAKET'),
(149, 'KOVACS INDOLE REAGEN', 0, 'PAKET'),
(150, 'BUFFERED PEPTONE WATER', 0, 'BOTOL'),
(151, 'VIOLET RED BILE GLUCOSE AGAR', 0, 'PAKET'),
(152, 'NUTRIENT AGAR', 0, 'PAKET'),
(153, 'OXIDASE DISCS', 0, 'PAKET'),
(154, 'MINERAL OIL', 0, 'PAKET'),
(155, 'COMPACT DRY EC', 0, 'PAKET'),
(156, 'COMPACT DRY ETB', 0, 'PAKET'),
(157, 'TRYPTIC SOY BROTH', 0, 'BOTOL'),
(158, 'TRYPTIC SOY AGAR', 0, 'BOTOL'),
(159, 'SABOURAUD 4%', 0, 'BOTOL'),
(160, 'TRYPTONE BILE GLUCURONIC AGAR', 0, 'BOTOL'),
(161, 'VIALBANK BACTERIAL STORAGE BEADS', 0, 'BOTOL'),
(162, 'BISMUTH III NITRATE BASIC', 1, 'BOTOL'),
(163, 'AMMONIA SOLUTION 32 %', 1, 'BOTOL'),
(164, 'PEPTONE WATER', 0, 'BOTOL'),
(165, 'GLUCOSE OF MEDIUM 500GR', 0, 'BOTOL'),
(166, 'Alopurinol', 1, 'pcs'),
(167, 'Alprazolam', 1, 'pcs'),
(168, 'Asam asetilsalisilat', 1, 'pcs'),
(169, 'Asam askorbat/Vitamin C', 1, 'pcs'),
(170, 'Asam Traneksamat', 1, 'pcs'),
(171, 'Bromazepam', 1, 'pcs'),
(172, 'CI 10020 Napthol green B/Acid green 1', 1, 'pcs'),
(173, 'CI 13065 Kuning metanil', 1, 'pcs'),
(174, 'CI 16185 Amarant', 1, 'pcs'),
(175, 'CI 42045 Acid blue 1', 1, 'pcs'),
(176, 'CI 42053 Fast green FCF', 1, 'pcs'),
(177, 'CI 45170 Rhodamin B/Merah K10/Basic violet 10', 1, 'pcs'),
(178, 'CI 59040 Solvent green 7', 1, 'pcs'),
(179, 'Diazepam', 1, 'pcs'),
(180, 'Diklofenak natrium', 1, 'pcs'),
(181, 'Etambutol hidroklorida', 1, 'pcs'),
(182, 'Fenilbutason', 1, 'pcs'),
(183, 'Indometasin', 1, 'pcs'),
(184, 'Klorfeniramin maleat/CTM', 1, 'pcs'),
(185, 'Kofein', 1, 'pcs'),
(186, 'Meloksikam', 1, 'pcs'),
(187, 'Metampiron/Metamizol natrium/Antalgin', 1, 'pcs'),
(188, 'Metformin hidroklorida', 1, 'pcs'),
(189, 'Metilprednisolon', 1, 'pcs'),
(190, 'Metiltestosteron', 1, 'pcs'),
(191, 'Metronidazol', 1, 'pcs'),
(192, 'Natrium tetraborat/Boraks', 1, 'pcs'),
(193, 'Nikotinamida/Vitamin B3', 1, 'pcs'),
(194, 'Parasetamol', 1, 'pcs'),
(195, 'Piridoksin hidroklorida/Vitamin B6', 1, 'pcs'),
(196, 'Piroksikam', 1, 'pcs'),
(197, 'Riboflavin/Vitamin B2', 1, 'pcs'),
(198, 'Sianokobalamin/Vitamin B12', 1, 'pcs'),
(199, 'Sildenafil Sitrat', 1, 'pcs'),
(200, 'Simvastatin', 1, 'pcs'),
(201, 'Tadalafil/Tadanafil', 1, 'pcs'),
(202, 'Tiamin hidroklorida/Vitamin B1', 1, 'pcs'),
(203, 'Tramadol hidroklorida', 1, 'pcs'),
(204, 'Triheksifenidil hidroklorida', 1, 'pcs'),
(205, 'Yohimbin hidroklorida', 1, 'pcs'),
(206, 'Klebsiella aerogenes NCTC 10006 (setara ATCC 13048)', 0, 'Vial'),
(207, 'Escherichia coli WDCM 00013 (setara ATCC 25922)', 1, 'Vial'),
(208, 'Escherichia coli WDCM 00012 (setara ATCC 8739)', 1, 'Vial'),
(209, 'Salmonella Typhimurium ATCC 14028', 1, 'Vial'),
(210, 'Shigella sonnei ATCC 9290', 1, 'Vial'),
(211, 'Candida albicans WDCM 00054 (setara ATCC 10231)', 1, 'Vial'),
(212, 'Listeria monocytogenes ATCC 7644', 1, 'Vial'),
(213, 'Apsergillus brasiliensis WDCM 00053 (setara ATCC 16404)', 1, 'Vial'),
(214, 'Pseudomonas aeruginosa WDCM 00026 (setara ATCC 9027)', 1, 'Vial'),
(215, 'Bacillus cereus WDCM 00001 (setara ATCC 11778)', 1, 'Vial'),
(216, 'Staphylococcus aureus ATCC 25923', 1, 'Vial'),
(217, 'Staphylococcus aureus WDCM 00032 (setara ATCC 6538)', 1, 'Vial'),
(218, 'PIPET TETES PLASTIK 5 ML', 0, 'BUNGKUS'),
(219, 'MEASURING CYLINDER 100 ML WITH PLASTIK STOPPER', 0, 'PCS'),
(220, 'SPATULA DOUBLE SPOON RIGIR', 0, 'PCS'),
(221, 'BEAKER PP 1000 ML BLUE SCALE', 0, 'PCS'),
(222, 'VOLUMETRIC PIPET 0,5 ML', 0, 'PCS'),
(223, 'FORCEP ANATOMIC L 200 MM', 0, 'PCS'),
(224, 'SEPARATING FUNNEL WITH TEFLON STOPCOCK 250 ML STOPPER', 0, 'PCS'),
(225, 'Measuring cylinder 50ml', 6, 'pcs'),
(226, 'Measuring cylinderr 100ml', 12, 'pcs'),
(227, 'Measuring cylinderr 1000ml', 2, 'pcs'),
(228, 'Measuring cylinder 250 ml', 6, 'pcs'),
(229, 'Measuring cylinderr 500ml', 4, 'pcs'),
(230, 'Beaker Low Form 20ml', 0, 'pcs'),
(231, 'Beaker Low Form 50 ml', 0, 'pcs'),
(232, 'Beakerr Low Form 1000ml', 0, 'pcs'),
(233, 'erlenmeyer flask with screw cap 250ml', 0, 'pcs'),
(234, 'erlenmeyerr flask 250ml', 0, 'pcs'),
(235, 'erlenmeyerr flask 500ml', 0, 'pcs'),
(236, 'Volumetric Pipet 1ml', 2, 'pcs'),
(237, 'Volumetric Pipet 5ml', 2, 'pcs'),
(238, 'Volumetricc Pipet 10ml', 2, 'pcs'),
(239, 'Volumetricc Pipet 25 ml', 1, 'pcs'),
(240, 'Measuring pipet sero type 1 ml', 2, 'pcs'),
(241, 'Measuring pipet sero type 10ml', 12, 'pcs'),
(242, 'vol flask with plastic stopper 10ml', 6, 'pcs'),
(243, 'vol flask with plastic stopper 20ml', 6, 'pcs'),
(244, 'vol flask with plastic stopper 50ml', 6, 'pcs'),
(245, 'vol flask with plastic stopper 100ml', 6, 'pcs'),
(246, 'Vol Flask with Plastic stopper 250 ml', 6, 'pcs'),
(247, 'Test Tube with screw cap 22ml 16x160 mm', 0, 'pcs'),
(248, 'petridishh steripain soda lime 90 x 15 mm', 0, 'pcs'),
(249, 'Evaporating Basin Franch Shape Vol 60ml', 0, 'pcs'),
(250, 'pipet filler standar', 0, 'pcs'),
(251, 'Statif and Clamp Buratte Singlee', 2, 'pcs'),
(252, 'Spatula Sendok Stainles 18cm', 0, 'pcs'),
(253, 'Vol Flask with plastic stopper 10ml Amber Color', 6, 'pcs'),
(254, 'Vol Flask with plastic stopper 20ml Amber Color', 6, 'pcs'),
(255, 'Vol Flask with plastic stopper 25ml Amber Color', 6, 'pcs'),
(256, 'Vol Flask with plastic stopper 50ml Amber Color', 6, 'pcs'),
(257, 'Vol Flask with plastic stopper 100ml Amber Color', 6, 'pcs'),
(258, 'Vol Flask with plastic stopper 250ml Amber Color', 6, 'pcs'),
(259, 'Volumetricc Pipet 2,5ml', 2, 'pcs'),
(260, 'Vol Flask with Plastic Stopper 25ml', 6, 'pcs'),
(261, 'Evaporating Basin Franch Shape Vol 100ml', 0, 'pcs'),
(262, 'Measuring Pipet sero type 5ml', 2, 'pcs'),
(263, 'Humidity Chambers', 2, 'Unit'),
(264, 'Test Kit Rhodamin B (TIDAK DIGUNAKAN)', 0, 'Paket'),
(265, 'Test Kit Boraks (TIDAK DIGUNAKAN)', 0, 'Paket'),
(266, 'Test Kit Formalin (TIDAK DIGUNAKAN)', 0, 'Paket'),
(267, 'Test Kit Methanyl Yellow (TIDAK DIGUNAKAN)', 0, 'Paket'),
(268, 'Wash Bottle', 0, 'Buah'),
(269, 'Rak Tabung', 0, 'Buah'),
(270, 'Tas Wadah', 0, 'Buah'),
(271, 'Gelas Ukur 10 Ml', 20, 'Buah'),
(272, 'Gelas Ukur 25 mL', 9, 'Buah'),
(273, 'Gelas Ukur 50 mL', 5, 'Buah'),
(274, 'Gelas Ukur 100 Ml', 3, 'Pcs'),
(275, 'Pembatas Kertas', 0, 'Bungkus'),
(276, 'Sheet Protector', 16, 'Bungkus'),
(277, 'Sticky Note', 13, 'Bungkus'),
(278, 'Map L (TIDAK DIGUNAKAN)', 0, 'Bungkus'),
(279, 'PULPEN', 18, 'PCS'),
(280, 'SPIDOL', 51, 'PCS'),
(281, 'PENSIL', 0, 'DOS'),
(282, 'Binder Clips 107 (TIDAK DIGUNAKAN)', 0, 'Pcs'),
(283, 'BINDER CLIPS 300', 3, 'Pcs'),
(284, 'Binder Clip 200 (TIDAK DIGUNAKAN)', 0, 'Pcs'),
(285, 'Penjepit Kertas No.5', 0, 'Box'),
(286, 'Penjepit Kertas No.1', 0, 'Box'),
(287, 'BINDER CLIPS 105', 12, 'PCS'),
(288, 'BINDER CLIPS 107', 32, 'PCS'),
(289, 'BINDER CLIPS 111', 0, 'PCS'),
(290, 'BINDER CLIPS 200', 3, 'PCS'),
(291, 'PENJEPIT KERTAS NO 3', 0, 'PCS'),
(292, 'BINDER CLIPS 155', 8, 'PCS'),
(293, 'Penghapus Pensil', 213, 'Pcs'),
(294, 'Tipe X', 0, 'Pcs'),
(295, 'Buku', 10, 'pcs'),
(296, 'Map Plastik L', 6, 'Bungkus'),
(297, 'Map Folder Ordner', 0, 'Pcs'),
(298, 'Boks Arsip', 0, 'Buah'),
(299, 'Map Folder', 0, 'Buah'),
(300, 'Map Gantung', 90, 'Buah'),
(301, 'MAP KANCING', 3, 'PCS'),
(302, 'CLIPBOARD MAP BANTEX', 1, 'PCS'),
(303, 'MAP BPOM', 7, 'Pcs'),
(304, 'Mistar Besi 30 CM', 0, 'Pcs'),
(305, 'Cutter', 0, 'Pcs'),
(306, 'Isolasi Bening BPOM', 20, 'Pcs'),
(307, 'Staples', 5, 'Pcs'),
(308, 'Staples No.10', 0, 'Pcs'),
(309, 'Staples No.3', 0, 'Pcs'),
(310, 'TC COLOR GUIDE EDISI 2002', 0, 'PCS'),
(311, 'Pelubang Kertas', 0, 'Pcs'),
(312, 'Lakban Hitam', 0, 'Pcs'),
(313, 'Isolasi Bening', 5, 'Pcs'),
(314, 'Pembatas Buku', 0, 'Pasang'),
(315, 'Rautan Pensil', 0, 'Pcs'),
(316, 'Gunting Besar', 0, 'Pcs'),
(317, 'Gunting Kecil', 0, 'Pcs'),
(318, 'Lem Kertas', 4, 'Pcs'),
(319, 'Spidol Permanen', 0, 'Pcs'),
(320, 'Spidol Whiteboard (TIDAK DIGUNAKAN)', 0, 'Pcs'),
(321, 'Stabilo', 18, 'Pcs'),
(322, 'Penghapus Papan Tulis', 0, 'Pcs'),
(323, 'PEMBATAS KERTAS_IM 30', 0, 'PCS'),
(324, 'PEMBATAS KERTAS_IM 42', 0, 'PCS'),
(325, 'ISOLASI BENING BESAR', 4, 'PCS'),
(326, 'DOUBLE TAPE', 1, 'PCS'),
(327, 'Kertas A4 80 Gram', 28, 'Rim'),
(328, 'Kertas F4 80 Gram (TIDAK DIGUNAKAN)', 0, 'Rim'),
(329, 'KERTAS F4 80 GRAM', 3, 'RIM'),
(330, 'KERTAS FOTO', 0, 'RIM'),
(331, 'Kartu Stock Opname', 1, 'Pack'),
(332, 'Amplop kecil', 3, 'box'),
(333, 'Amplop Besar (TIDAK DIGUNAKAN)', 0, 'Box'),
(334, 'Amplop Cokelat', 0, 'Bungkus'),
(335, 'AMPLOP PANJANG', 5, 'BOX'),
(336, 'Stik Note Besar', 0, 'pcs'),
(337, 'Stik Note Kecil', 0, 'Pcs'),
(338, 'Tinta Epson 001', 0, 'Pcs'),
(339, 'Tinta Epson 003', 1, 'Pcs'),
(340, 'Cartridge 955XL Hitam', 0, 'Pcs'),
(341, 'Tinta Epson WF-100 Warna', 0, 'Pcs'),
(342, 'Tinta Epson WF-100 Hitam', 0, 'Pcs'),
(343, 'Cartridge 955XL Cyan', 0, 'Pcs'),
(344, 'Cartridge 955XL Magenta', 0, 'Pcs'),
(345, 'Cartridge 955Xl Yellow', 0, 'Pcs'),
(346, 'Tinta Autoprint 644', 0, 'Pcs'),
(347, 'Tinta AutoPrint Black', 1, 'Pcs'),
(348, 'Tinta Autoprint Cyan', 1, 'Pcs'),
(349, 'Tinta Aratech 003 BK', 0, 'Pcs'),
(350, 'Tinta Aratech 003 Cyan', 0, 'Pcs'),
(351, 'Tinta Aratech 003 Magenta', 1, 'Pcs'),
(352, 'Tinta Aratech 003 Yellow', 0, 'Pcs'),
(353, 'Tinta Aratech 664 BK', 0, 'Pcs'),
(354, 'Tinta Aratech 664 Cyan', 6, 'Pcs'),
(355, 'Tinta Aratech 664 Magenta', 6, 'Pcs'),
(356, 'Tinta Aratech 664 Yellow', 5, 'Pcs'),
(357, 'Tinta Canon Pixma 790', 7, 'Botol'),
(358, 'TINTA HP 955 XL', 2, 'DOS'),
(359, 'TINTA EPSON 289', 0, 'PCS'),
(360, 'INK CARTRIDE', 0, 'PCS'),
(361, 'Maintenance Box WF-100', 0, 'Pcs'),
(362, 'PEMBERSIH LAPTOP', 0, 'SET'),
(363, 'Tissu Gulung', 0, 'Bungkus'),
(364, 'Tissue 900g', 0, 'Bungkus'),
(365, 'Tissue 100g', 0, 'Bungkus'),
(366, 'Kamper Toilet', 0, 'Pcs'),
(367, 'SENDOK', 0, 'LUSIN'),
(368, 'GARPU', 0, 'LUSIN'),
(369, 'PIRING', 0, 'LUSIN'),
(370, 'GELAS', 0, 'LUSIN'),
(371, 'Balon Lampu', 3, 'Pcs'),
(372, 'Baterai AA', 0, 'Lusin'),
(373, 'Baterai C', 0, 'Lusin'),
(374, 'BATERAI', 6, 'Pcs'),
(375, 'TERMINAL', 0, 'PCS'),
(376, 'BAJU LABORATORIUM', 0, 'PCS'),
(377, 'Meterai', 6, 'Pcs'),
(378, 'Pembersih Lantai', 0, 'Bungkus'),
(379, 'Tissu Toilet', 0, 'Pcs'),
(380, 'Batang Penganduk Pendek', 0, 'Buah'),
(381, 'Beaker Glass 100 Ml', 0, 'Pcs'),
(382, 'Beaker glass 250 ML', 0, 'Pcs'),
(383, 'Beaker Glass 500 Ml', 0, 'Pcs'),
(384, 'Erlenmeyer 100 Ml', 0, 'Pcs'),
(385, 'Erlenmeyer 250 ML', 0, 'Pcs'),
(386, 'Erlenmeyer 300 Ml', 0, 'Pcs'),
(387, 'Mortal Lumpang 75 Ml', 0, 'Pcs'),
(388, 'Pestle Diameter (Alu)', 0, 'Pcs'),
(389, 'Safety Glass', 0, 'Pcs'),
(390, 'Test Tube', 0, 'Pcs'),
(391, 'Botol Semprot', 0, 'Pcs'),
(392, 'Pipet Tetes', 0, 'Pcs'),
(393, 'Spoit 10 Ml', 0, 'Pcs'),
(394, 'Timbangan Digital', 0, 'Pcs'),
(395, 'Masker', 8, 'Box'),
(396, 'Tabung Reaksi Dg Penutup 12 ML_DURAN', 0, 'Pcs'),
(397, 'SANDAL LAB', 0, 'Pasang'),
(398, 'SARUNG TANGAN', 2, 'Pasang'),
(399, 'LAP', 0, 'PCS'),
(400, 'MEASURING CYLINDER 50 ML', 12, 'PCS'),
(401, 'JERGEN PUTIH 20L', 0, 'PCS'),
(402, 'TAKARAN 1L', 0, 'PCS'),
(403, 'YELLOW TIP', 0, 'PCS'),
(404, 'ERLENMEYER 1 L', 0, 'PCS'),
(405, 'BLUE TIP', 0, 'PCS'),
(406, 'NURSE CUP', 3, 'PCS'),
(407, 'KERANJANG OBAT', 0, 'PCS'),
(408, 'KACAMATA/ GOOGLE OM', 0, 'PCS'),
(409, 'MEASURING CYLINDER 100ML', 15, 'PCS'),
(410, 'MEASURING CYLINDER 250ML', 2, 'PCS'),
(411, 'MEASURING CYLINDER 500ML', 2, 'PCS'),
(412, 'MEASURING CYLINDER 1000ML', 1, 'PCS'),
(413, 'BEAKER LOW FORM 20 ML', 0, 'PCS'),
(414, 'BEAKER LOW FORM 1000ML', 0, 'PCS'),
(415, 'BEAKER LOW FORM 50ML', 0, 'PCS'),
(416, 'ERLENMEYER FLASK 250ML', 0, 'PCS'),
(417, 'ERLENMEYER FLASK  WITH SEREW CAP 250 ML', 0, 'PCS'),
(418, 'ERLENMEYER FLASK 500ML', 0, 'PCS'),
(419, 'MEASURING PIPETTE SERO TYPE 1 ML', 2, 'PCS'),
(420, 'VOLUMETRIC PIPET 2,5ML', 2, 'PCS'),
(421, 'VOLUMETRIC PIPET 25 ML', 1, 'PCS'),
(422, 'VOLUMETRIC PIPET 5 ML', 2, 'PCS'),
(423, 'VOLUMETRIC PIPET 10ML', 2, 'PCS'),
(424, 'MEASURING PIPETTE SERO TYPE 10 ML', 4, 'PCS'),
(425, 'MEASURING PIPETTE SERO TYPE 5 ML', 17, 'PCS'),
(426, 'VOL FLASK WITH PLASTIC STOPPER 10 ML', 9, 'PCS'),
(427, 'SEPARATING FUNNEL WITH TEFLON STOPCOCK 250 ML', 0, 'PCS'),
(428, 'VOL FLASK WITH PLASTIC STOPPER 20 ML', 9, 'PCS'),
(429, 'VOL FLASK PLASTIC STOPPER 25 ML', 9, 'PCS'),
(430, 'VOL FLASK PLASTIC STOPPER 50 ML', 9, 'PCS'),
(431, 'VOL FLASK PLASTIC STOPPER 250 ML', 9, 'PCS'),
(432, 'VOL FLASK PLASTIC STOPPER 100 ML', 9, 'PCS'),
(433, 'VOL FLASK PLASTIC STOPPER 10 ML AMBER COLOR', 6, 'PCS'),
(434, 'VOL FLASK PLASTIC STOPPER 20 ML AMBER COLOR', 6, 'PCS'),
(435, 'VOL FLASK PLASTIC STOPPER 25 ML AMBER COLOR', 6, 'PCS'),
(436, 'VOL FLASK PLASTIC STOPPER 50 ML AMBER COLOR', 6, 'PCS'),
(437, 'VOL FLASK PLASTIC STOPPER 1O0 ML AMBER COLOR', 6, 'PCS'),
(438, 'VOL FLASK PLASTIC STOPPER 250 ML AMBER COLOR', 6, 'PCS'),
(439, 'BURETTE, TEFLON STOPCOCK CLEAR 50 ML', 0, 'PCS'),
(440, 'BURETTE TEFLON STOPCOCK AMBER 50 ML', 0, 'PCS'),
(441, 'TEST TUBE WITH SEREW CAP 22 ML, 16X160 MM', 0, 'PCS'),
(442, 'PETRIDISH STERIPAIN SODA LIME 90 X 15 MM', 0, 'PCS'),
(443, 'PETRIDISH DISPOSIBLE STEILE 90 X 15 MM', 0, 'PCS'),
(444, 'PIPES TO STERILIZE PIPPETES 60X500 MM', 0, 'PCS'),
(445, 'LABORATORY BOTTLE SCREW CAP 100 ML', 0, 'PCS'),
(446, 'LABORATORY BOTTLE SCREW CAP 250 ML', 0, 'PCS'),
(447, 'LABORATORY BOTTLE SCREW CAP 500 ML', 0, 'PCS'),
(448, 'LABORATORY BOTTLE SCREW CAP 1000 ML', 0, 'PCS'),
(449, 'EVAPORATING BASIN FRANCH SHAPE VOL 60 ML', 0, 'PCS'),
(450, 'PIPETTE FILLER STANDAR', 0, 'PCS'),
(451, 'PIPET TETES KACA DOT MERAH 20 CM', 0, 'PCS'),
(452, 'RACK FOR FUNNEL PVC 500-1000 ML', 0, 'PCS'),
(453, 'THERMO-HYGROMETER DIGITAL DUAL DISPLAY', 0, 'PCS'),
(454, 'STATIF AND CLAMP BURATTE SINGLE', 0, 'PCS'),
(455, 'SPATULA WITH DOUBLE SPOON 18 CM', 0, 'PCS'),
(456, 'SPATULA SENDOK STAINLESS 18 CM', 0, 'PCS'),
(457, 'RACK TEST TUBE STAINLESS 12 HOLE 18 MM', 0, 'PCS'),
(458, 'BAGRACK 400 ML', 0, 'PCS'),
(459, 'GUNTING ALUMINIUM 14 CM', 0, 'PCS'),
(460, 'GLASS ROD SPREADER 7X 120 MM', 0, 'PCS'),
(461, 'PIPETTE SUPPORT RACK', 0, 'PCS'),
(462, 'FUNNEL KACA 50 MM', 0, 'PCS'),
(463, 'TABUNG  DURHAM 6 X 35 MM', 0, 'PCS'),
(464, 'EVAPORATING BASIN FRANCH SHAPE VOL 100 ML', 0, 'PCS'),
(465, 'VOLUMETRIC PIPET 1 ML', 2, 'PCS'),
(466, 'PIPET TO STERILIZE 60 X 50 MM', 0, 'PCS'),
(467, 'KERTAS KOPI', 19, 'LEMBAR'),
(468, 'rack test tube 15 mm', 0, 'PCS'),
(469, 'YELLOW TIP GILSON SCALA 200', 0, 'PACK'),
(470, 'TABUNG PETRIDISH 100 MM', 0, 'PCS'),
(471, 'BOTOL SPRAY 500 ML', 0, 'PCS'),
(472, 'BATANG PENGADUK KACA 30 CM', 0, 'PCS'),
(473, 'OSE 10 UL STERILE', 0, 'PCS'),
(474, 'BUNSEN SPIRITUS 250 ML', 0, 'PCS'),
(475, 'BOTOL VIAL KACA 10 ML', 0, 'PCS'),
(476, 'BOTOL VIAL KACA 5 ML', 0, 'PCS'),
(477, 'BOTOL VIAL KACA 20 ML', 0, 'PCS'),
(478, 'FUNNEL KACA 75 MM', 0, 'PCS'),
(479, 'THERMOMETER RAKSA MERCURY', 0, 'PCS'),
(480, 'CAWAN POSELIN 100 ML', 0, 'PCS'),
(481, 'BOTOL SEMPROT 500 ML', 0, 'PCS'),
(482, 'DESTILATION SPRAYER DAN BULB RUBBER', 0, 'PCS'),
(483, 'PINSET ANATOMIC 14 CM', 0, 'PCS'),
(484, 'PINSET ANATOMIC 25 CM', 0, 'PCS'),
(485, 'PINSET ANATOMIC 20 CM', 0, 'PCS'),
(486, 'SIKAT TEST TUBE', 0, 'PCS'),
(487, 'PINSET SIKU STAILESS STEEL LABORATORIUM', 0, 'PCS'),
(488, 'DISPOSIBLE GLASS CAPILLARIES 2 UL', 0, 'PCS'),
(489, 'DISPOSIBLE GLASS CAPILLARIES 5 UL', 0, 'PCS'),
(490, 'SYRINGE 10 UL', 0, 'PCS'),
(491, 'DISPOSIBLE MICROPIPET TIP BIRU 1000 UL', 0, 'PCS'),
(492, 'INTERSCIENCE BAGFILTER 400 P', 0, 'PCS'),
(493, 'BAGCLIP FOR 400 ML', 0, 'PCS'),
(494, 'RAK SUSUN', 0, 'PCS'),
(495, 'SILICA GEL', 5, 'PAKET'),
(496, 'ALUMINIUM FOIL', 0, 'PCS'),
(497, 'KARET GELANG', 0, 'BUNGKUS'),
(498, 'KERTAS SARING BIASA 60 X 60 CM', 0, 'LEMBAR'),
(499, 'SPILL KIT BIOHAZARD', 1, 'PAKET'),
(500, 'BLUE TIP GILSON SCALA 1000', 0, 'PACK'),
(501, 'Sabun Cuci Piring', 0, 'Pcs'),
(502, 'Teh', 0, 'Bungkus'),
(503, 'Gula Pasir', 0, 'Bungkus'),
(504, 'Permen', 0, 'Bungkus'),
(505, 'Air Mineral', 0, 'Box'),
(506, 'Keranjang Kecil', 0, 'Pcs'),
(507, 'Sabun Mandi', 0, 'Pcs'),
(508, 'Sabun Cuci Piring', 0, 'Pcs'),
(509, 'Sabun Cuci Tangan', 0, 'Pcs'),
(510, 'Tissu Besar (900g)', 0, 'Bungkus'),
(511, 'Sekat/Guide', 17, 'Buah'),
(512, 'POUCH', 0, 'PCS');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_aset`
--

CREATE TABLE `riwayat_aset` (
  `id` int(11) NOT NULL,
  `aset_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis_transaksi` enum('peminjaman','pengembalian') NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `nomor_dokumen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_persediaan`
--

CREATE TABLE `riwayat_persediaan` (
  `id` int(11) NOT NULL,
  `persediaan_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis_transaksi` enum('masuk','keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `nomor_dokumen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nomor_telepon` varchar(20) NOT NULL,
  `peran` enum('admin') NOT NULL DEFAULT 'admin',
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nomor_telepon`, `peran`, `nama_lengkap`, `email`, `no_telepon`, `alamat`) VALUES
(1, 'admin', '$2y$10$lUjTKtheUD7ozXWuZzz6B.ByLmBqKVJPHiIo/hJbbA.D6AEOcNuyi', '6281356773026', 'admin', 'Doddy Prayudi', 'prayudid6@gmail.com', '081356773026', 'Jl. Dr Ratulangi, Salobulo, Kec. Wara Utara, Kota Palopo');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aset`
--
ALTER TABLE `aset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_bmn` (`no_bmn`);

--
-- Indexes for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjaman_id` (`peminjaman_id`),
  ADD KEY `aset_id` (`aset_id`);

--
-- Indexes for table `detail_permintaan`
--
ALTER TABLE `detail_permintaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permintaan_id` (`permintaan_id`),
  ADD KEY `persediaan_id` (`persediaan_id`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_pengaturan` (`nama_pengaturan`);

--
-- Indexes for table `permintaan_persediaan`
--
ALTER TABLE `permintaan_persediaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `persediaan`
--
ALTER TABLE `persediaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `riwayat_aset`
--
ALTER TABLE `riwayat_aset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aset_id` (`aset_id`);

--
-- Indexes for table `riwayat_persediaan`
--
ALTER TABLE `riwayat_persediaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `persediaan_id` (`persediaan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aset`
--
ALTER TABLE `aset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `detail_permintaan`
--
ALTER TABLE `detail_permintaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `permintaan_persediaan`
--
ALTER TABLE `permintaan_persediaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `persediaan`
--
ALTER TABLE `persediaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=513;

--
-- AUTO_INCREMENT for table `riwayat_aset`
--
ALTER TABLE `riwayat_aset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `riwayat_persediaan`
--
ALTER TABLE `riwayat_persediaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`aset_id`) REFERENCES `aset` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `detail_permintaan`
--
ALTER TABLE `detail_permintaan`
  ADD CONSTRAINT `detail_permintaan_ibfk_1` FOREIGN KEY (`permintaan_id`) REFERENCES `permintaan_persediaan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_permintaan_ibfk_2` FOREIGN KEY (`persediaan_id`) REFERENCES `persediaan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_aset`
--
ALTER TABLE `riwayat_aset`
  ADD CONSTRAINT `riwayat_aset_ibfk_1` FOREIGN KEY (`aset_id`) REFERENCES `aset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_persediaan`
--
ALTER TABLE `riwayat_persediaan`
  ADD CONSTRAINT `riwayat_persediaan_ibfk_1` FOREIGN KEY (`persediaan_id`) REFERENCES `persediaan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
