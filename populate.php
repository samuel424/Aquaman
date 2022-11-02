<?php

include 'connect.php';
$link = connect();

// Account
    $val = mysqli_query($link, "SELECT 1 FROM Account"); //Pwd is the hash of the password
    if($val == FALSE){
        $sql = "CREATE TABLE Account (
            UserID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            Firstname VARCHAR(30),
            Lastname VARCHAR(30),
            Email VARCHAR(320) UNIQUE,
            Pwd VARCHAR(128), 
            UserRole VARCHAR(50)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// Temporary
    $val = mysqli_query($link, "SELECT 1 FROM Temporary"); //same as Account but used for temporary storage
    if($val == FALSE){
        $sql = "CREATE TABLE Temporary (
            TempID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            Firstname VARCHAR(30),
            Lastname VARCHAR(30),
            Email VARCHAR(320) UNIQUE,
            Pwd VARCHAR(128),
            confirmhash VARCHAR(128)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

//LABORATORY
    $val = mysqli_query($link, "SELECT 1 FROM Laboratory");
    if($val == FALSE){
        $sql = "CREATE TABLE Laboratory (
            LabID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            LabName VARCHAR(30),
            Country VARCHAR(40),
            City VARCHAR(40),
            LabAddress VARCHAR(100),
            MainAccount INT,
            FOREIGN KEY (MainAccount) REFERENCES Account(UserID)
        )"; 
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

//Lab affiliation
    $val = mysqli_query($link, "SELECT 1 FROM LabAffiliation");
    if($val == FALSE){
        $sql = "CREATE TABLE LabAffiliation (
            UserID INT NOT NULL,
            LabID INT NOT NULL,
            LabRole TINYINT  NOT NULL DEFAULT 0,
            PRIMARY KEY (UserID, LabID),
            FOREIGN KEY (UserID) REFERENCES Account(UserID),
            FOREIGN KEY (LabID) REFERENCES Laboratory(LabID)
        )";
        //Labrole :0 = researcher, 1 = admin
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// SamplingLocation
    $val = mysqli_query($link, "SELECT 1 FROM SamplingLocation");
    if($val == FALSE){
        $sql = "CREATE TABLE SamplingLocation (
            LocationID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            LocationName VARCHAR(30),
            CorLatitude VARCHAR(10),
            CorLongitude VARCHAR(10)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// Field Sampling
    $val = mysqli_query($link, "SELECT 1 FROM FieldSampling");
    if($val == FALSE){
        $sql = "CREATE TABLE FieldSampling (
            SamplingID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            LeaderID INT,
            LocationID INT,
            STimestamp DATETIME,
            pH FLOAT,
            Oxygen INT,
            Hg FLOAT,
            Pb FLOAT,
            FOREIGN KEY (LeaderID) REFERENCES Account(UserID),
            FOREIGN KEY (LocationID) REFERENCES SamplingLocation(LocationID)
        )"; //depth data useful for actual research, not necessary for our proof of concept
            //separate table for depth-specific chemical measurements
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// Noise
    $val = mysqli_query($link, "SELECT 1 FROM Noise");
    if($val == FALSE){
        $sql = "CREATE TABLE Noise (
            LocationID INT,
            NDate DATE,
            ReporterID INT,
            NoiseRank INT,
            PRIMARY KEY (LocationID, NDate),
            FOREIGN KEY (ReporterID) REFERENCES Account(UserID),
            FOREIGN KEY (LocationID) REFERENCES SamplingLocation(LocationID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    } 

//Species
    $val = mysqli_query($link, "SELECT 1 FROM Species");
    if($val == FALSE){
        $sql = "CREATE TABLE Species (
            SpeciesID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            SpeciesLatin VARCHAR(50),
            SpeciesEnglish VARCHAR(50)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

//Catchmethod
    $val = mysqli_query($link, "SELECT 1 FROM Catchmethod");
    if($val == FALSE){
        $sql = "CREATE TABLE Catchmethod (
            CatchmethodID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            CatchmethodName VARCHAR(50)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// Fish
    $val = mysqli_query($link, "SELECT 1 FROM FishIndividual");
    if($val == FALSE){
        $sql = "CREATE TABLE FishIndividual (
            FishID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            FishSamplingID INT,
            Sex char(1),
            Species INT,
            Catchmethod INT,
            EnteredByUser INT,
            FOREIGN KEY (FishSamplingID) REFERENCES FieldSampling(SamplingID),
            FOREIGN KEY (Species) REFERENCES Species(SpeciesID),
            FOREIGN KEY (Catchmethod) REFERENCES Catchmethod(CatchmethodID),
            FOREIGN KEY (EnteredByUser) REFERENCES Account(UserID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// Ssample
    $val = mysqli_query($link, "SELECT 1 FROM Ssample");
    if($val == FALSE){
        $sql = "CREATE TABLE Ssample (
            SsampleID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            SFishID INT,
            DissectTime DATETIME,
            DissectUser INT,
            SType varchar(50),
            SLabID INT,
            FOREIGN KEY (SFishID) REFERENCES FishIndividual(FishID),
            FOREIGN KEY (DissectUser) REFERENCES Account(UserID),
            FOREIGN KEY (SLabID) REFERENCES Laboratory(LabID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// RNAsample
    $val = mysqli_query($link, "SELECT 1 FROM RNAsample");
    if($val == FALSE){
        $sql = "CREATE TABLE RNASample (
            RNAID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            RSampleID INT,
            ExtractionTimestamp DATETIME,
            ExtractionUserID INT,
            ExtractionLabID INT,
            ExtractionKit VARCHAR(50),
            ExtractionKitLotnumber VARCHAR(20),
            DNaseKit VARCHAR(50),
            DNaseLot VARCHAR(20),
            FOREIGN KEY (RSampleID) REFERENCES Ssample(SsampleID),
            FOREIGN KEY (ExtractionUserID) REFERENCES Account(UserID),
            FOREIGN KEY (ExtractionLabID) REFERENCES Laboratory(LabID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// Freezer
    $val = mysqli_query($link, "SELECT 1 FROM Freezer");
    if($val == FALSE){
        $sql = "CREATE TABLE Freezer (
            FreezerID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            FModel VARCHAR(30),
            FLabID INT,
            FOREIGN KEY (FLabID) REFERENCES Laboratory(LabID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }

    }


// Freeze
    $val = mysqli_query($link, "SELECT 1 FROM Freeze");
    if($val == FALSE){
        $sql = "CREATE TABLE Freeze (
            FreezeID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            FrFreezer INT,
            Temperature INT,
            FrInTime DATETIME,
            FrOutTime DATETIME,
            FrType INT,
            FrFish INT,
            FrSample INT,
            FrRNA INT,
            FOREIGN KEY (FrFreezer) REFERENCES Freezer(FreezerID),
            FOREIGN KEY (FrFish) REFERENCES FishIndividual(FishID),
            FOREIGN KEY (FrSample) REFERENCES Ssample(SsampleID),
            FOREIGN KEY (FrRNA) REFERENCES RNASample(RNAID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// qPCRmachine
    $val = mysqli_query($link, "SELECT 1 FROM qPCRmachine");
    if($val == FALSE){
        $sql = "CREATE TABLE qPCRmachine (
            MachineID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            MachineModel VARCHAR(50),
            MachineLab INT,
            FOREIGN KEY (MachineLab) REFERENCES Laboratory(LabID)    
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

    //qPCR cycling conditions
    $val = mysqli_query($link, "SELECT 1 FROM Cycling");
    if($val == FALSE){
        $sql = "CREATE TABLE Cycling (
            CyclingID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            CyclingCond VARCHAR(100)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }
    

// qPCR run
    $val = mysqli_query($link, "SELECT 1 FROM qPCRrun");
    if($val == FALSE){
        $sql = "CREATE TABLE qPCRrun (
            qPCRrunID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            qPCRTime DATETIME,
            qPCRSuccess TINYINT,
            qPCRUser INT,
            qPCRKit VARCHAR(50),
            qPCRMachine INT,
            Cycling INT,
            FOREIGN KEY (qPCRUser) REFERENCES Account(UserID),
            FOREIGN KEY (qPCRMachine) REFERENCES qPCRmachine(MachineID),
            FOREIGN KEY (Cycling) REFERENCES Cycling(CyclingID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// Gene
    $val = mysqli_query($link, "SELECT 1 FROM Gene");
    if($val == FALSE){
        $sql = "CREATE TABLE Gene (
            GeneID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            GName VARCHAR(30),
            TargetSpecies INT,
            Housekeeping TINYINT,
            FOREIGN KEY (TargetSpecies) REFERENCES Species(SpeciesID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }

// Probe
    $val = mysqli_query($link, "SELECT 1 FROM Probe");
    if($val == FALSE){
        $sql = "CREATE TABLE Probe (
            ProbeID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            ProbeName VARCHAR(30),
            PrType VARCHAR(1),
            ProbeSequence VARCHAR(40),
            TargetGene INT,
            Fluor VARCHAR(10),
            FOREIGN KEY (TargetGene) REFERENCES Gene(GeneID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }
    // Add default

// qPCR data
    $val = mysqli_query($link, "SELECT 1 FROM qPCRdata");
    if($val == FALSE){
        $sql = "CREATE TABLE qPCRdata (
            qPCRID INT,
            WellPos VARCHAR(3),
            qPCRRNA INT,
            PrimerF INT,
            PrimerR INT,
            Probe INT,
            CT FLOAT,
            SampleType TINYINT,
            CurveAnalyst INT,
            PRIMARY KEY (qPCRID, WellPos),
            FOREIGN KEY (qPCRID) REFERENCES qPCRrun(qPCRrunID),
            FOREIGN KEY (qPCRRNA) REFERENCES RNASample(RNAID),
            FOREIGN KEY (PrimerF) REFERENCES Probe(ProbeID),
            FOREIGN KEY (PrimerR) REFERENCES Probe(ProbeID),
            FOREIGN KEY (Probe) REFERENCES Probe(ProbeID),
            FOREIGN KEY (CurveAnalyst) REFERENCES Account(UserID)
        )";
        if (mysqli_query($link, $sql)){
            print "Table created successfully";
        } else {
            print "ERROR: Unable to execute $sql. " . mysqli_error($link);
        }
    }


// Add default
$arr = array(array('Danio rerio', 'Zebrafish'), array('Salmo salar', 'Atlantic salmon'));
foreach ($arr as &$val1) {
    
    $sql_ = "SELECT SpeciesID FROM Species WHERE SpeciesLatin = '".$val1[0]."'";
    if (!mysqli_query($link, $sql_)) {
        $sql = "INSERT INTO Species (`SpeciesLatin`, `SpeciesEnglish`) VALUES '(".$val1[0].", ".$val1[1].")'";
        if (mysqli_query($link, $sql)){
            print $val1[0]." successfully added <br>";
        } else{
            print "ERROR:".mysqli_error($link);
        }
    } else {
        echo $val1[0]." already in db <br>";
    }
    $sID = mysqli_fetch_row(mysqli_query($link, $sql_))[0];

    foreach (array('hsp70','sod1','igf1','elfa') as &$val2) {
        if ($val2 == 'elfa') {
            $hk = 1;
        } else {
            $hk = 0;
        }
        
        $sql_ = "SELECT GeneID FROM Gene WHERE TargetSpecies = $sID AND GName = '$val2'";
        if (mysqli_num_rows(mysqli_query($link, $sql_)) < 1) {$sql = "INSERT INTO Gene (GName, TargetSpecies, Housekeeping) VALUES ('$val2', $sID, $hk)";
            if (mysqli_query($link, $sql)){
                print $val2." successfully added <br>";
            } else{
                print "ERROR:".mysqli_error($link)."<br>";
            }
        } else {
            echo "$val2(".$val1[0].") already in db <br>";
        }

        //$gID = mysqli_fetch_row(mysqli_query($link, $sql_))[0];
    }
}


dconnect($link);
?>