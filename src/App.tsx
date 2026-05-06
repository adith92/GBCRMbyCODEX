import React, { useState, useEffect, useMemo } from "react";
import { 
  Users, 
  Car, 
  UserSquare2, 
  ClipboardList, 
  LayoutDashboard, 
  Search, 
  Bell, 
  CreditCard,
  Database,
  Menu,
  X,
  ChevronRight,
  TrendingUp,
  AlertCircle,
  CheckCircle2,
  Clock,
  LogIn,
  LogOut,
  CalendarDays,
  ArrowUpRight,
  ArrowDownRight,
  ArrowUpDown,
  ArrowUp,
  ArrowDown
} from "lucide-react";
import { 
  ResponsiveContainer, 
  AreaChart, 
  Area, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  BarChart, 
  Bar, 
  Cell
} from "recharts";
import { 
  startOfDay, 
  startOfWeek, 
  startOfMonth, 
  startOfYear, 
  format, 
  subDays, 
  subWeeks, 
  subMonths, 
  subYears, 
  isSameDay, 
  isSameWeek, 
  isSameMonth, 
  isSameYear,
  eachDayOfInterval,
  eachWeekOfInterval,
  eachMonthOfInterval,
  endOfDay,
  endOfWeek,
  endOfMonth
} from "date-fns";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { 
  Card, 
  CardContent, 
  CardDescription, 
  CardHeader, 
  CardTitle 
} from "@/components/ui/card";
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from "@/components/ui/table";
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from "@/components/ui/breadcrumb";
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet";
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from "@/components/ui/tabs";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Label } from "@/components/ui/label";
import {
  CorporateClient,
  Vehicle,
  Driver,
  Order,
  EntityStatus,
  BusinessLine,
  Partner
} from "./types";
import { db, auth, signIn, handleFirestoreError, OperationType } from "./lib/firebase";
import { 
  collection, 
  onSnapshot, 
  addDoc, 
  updateDoc, 
  doc, 
  query, 
  orderBy, 
  where,
  serverTimestamp,
  setDoc
} from "firebase/firestore";
import { onAuthStateChanged, signOut, User } from "firebase/auth";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

// Mock Data Seeding (Internal)
const MOCK_CLIENTS: CorporateClient[] = [
  { id: "CL-001", name: "PT. Astra International", email: "procurement@astra.co.id", address: "Jl. Gaya Motor No. 8", status: "Active", createdAt: new Date() },
  { id: "CL-002", name: "PT. Telkom Indonesia", email: "b2b@telkom.co.id", address: "Gedung Telkom Landmark Tower", status: "Active", createdAt: new Date() },
  { id: "CL-003", name: "Shopee Indonesia", email: "fleet@shopee.co.id", address: "Pacific Century Place", status: "Active", createdAt: new Date() },
  { id: "CL-004", name: "Gojek Tokopedia", email: "ops@goto.com", address: "Pasaraya Blok M", status: "Inactive", createdAt: new Date() },
  { id: "CL-005", name: "PT. Bank Mandiri", email: "admin@mandiri.co.id", address: "Plaza Mandiri Gatot Subroto", status: "Active", createdAt: new Date() },
];

const MOCK_VEHICLES: Vehicle[] = [
  { id: "V-B1234AB", plateNumber: "B 1234 AB", model: "Toyota Camry", category: "Sedan", businessLine: "Bluebird", status: "Available", createdAt: new Date() },
  { id: "V-B5678CD", plateNumber: "B 5678 CD", model: "Alphard", category: "SUV", businessLine: "Silverbird", status: "Busy", createdAt: new Date() },
  { id: "V-B9012EF", plateNumber: "B 9012 EF", model: "Hino Bigbird", category: "Bus", businessLine: "Big Bird", status: "Maintenance", estimatedCompletionDate: "2026-05-10", createdAt: new Date() },
  { id: "V-B2345GH", plateNumber: "B 2345 GH", model: "Toyota Innova", category: "SUV", businessLine: "Goldenbird", status: "Available", createdAt: new Date() },
  { id: "V-B6789IJ", plateNumber: "B 6789 IJ", model: "Mercedes Benz E-Class", category: "Sedan", businessLine: "Silverbird", status: "Available", createdAt: new Date() },
  { id: "V-LOG-01", plateNumber: "B 4455 LOG", model: "Isuzu Giga", category: "Bus", businessLine: "Ironbird", status: "Available", createdAt: new Date() },
  { id: "V-KIRIM-01", plateNumber: "B 1122 KRM", model: "Toyota Blind Van", category: "Van", businessLine: "Bluebird Kirim", status: "Busy", createdAt: new Date() },
  { id: "V-MOBIL-01", plateNumber: "EX-DEMO-01", model: "Lexus RX", category: "SUV", businessLine: "BirdMobil", status: "Available", createdAt: new Date() },
];

const MOCK_DRIVERS: Driver[] = [
  { id: "D-001", name: "Budi Santoso", phone: "08123456789", status: "Available", createdAt: new Date() },
  { id: "D-002", name: "Slamet Rahardjo", phone: "08123456780", status: "Busy", assignedVehicleId: "V-B5678CD", createdAt: new Date() },
  { id: "D-003", name: "Andi Wijaya", phone: "08123456781", status: "Off", createdAt: new Date() },
];

const MOCK_ORDERS: Order[] = [
  { id: "ORD-1001", clientId: "CL-001", vehicleId: "V-B5678CD", driverId: "D-002", businessLine: "Silverbird", status: "Active", pickupDate: "2026-05-01", returnDate: "2026-05-05", price: 2500000, paymentStatus: "Outstanding", createdAt: new Date() },
  { id: "ORD-1002", clientId: "CL-002", vehicleId: "V-B1234AB", driverId: "D-001", businessLine: "Bluebird", status: "Completed", pickupDate: "2026-04-20", returnDate: "2026-04-22", price: 1500000, paymentStatus: "Paid", createdAt: new Date() },
  { id: "ORD-1003", clientId: "CL-001", vehicleId: "V-LOG-01", driverId: "D-001", businessLine: "Ironbird", status: "Active", pickupDate: "2026-05-01", returnDate: "2026-05-15", price: 45000000, paymentStatus: "Outstanding", createdAt: new Date() },
];

type View = "Dashboard" | "Clients" | "Fleet" | "Drivers" | "Sales" | "Finance" | "Logs" | "Partners" | "Users";
type UserRole = "GM" | "Finance" | "Operational" | "Sales";

interface LogEntry {
  id: string;
  timestamp: Date;
  user: string;
  action: string;
  module: View;
  details: string;
}

interface RolePermissions {
  views: View[];
}

const ROLE_PERMISSIONS: Record<UserRole, RolePermissions> = {
  GM: { views: ["Dashboard", "Sales", "Fleet", "Drivers", "Clients", "Finance", "Logs", "Partners", "Users"] },
  Finance: { views: ["Dashboard", "Finance", "Logs", "Partners"] },
  Operational: { views: ["Dashboard", "Fleet", "Drivers", "Logs"] },
  Sales: { views: ["Dashboard", "Clients", "Sales", "Logs"] },
};


export const MOCK_SALES_PEOPLE = [
  { id: "S-001", name: "Andri Setiawan" },
  { id: "S-002", name: "Siska Wijaya" },
  { id: "S-003", name: "Bambang Pamungkas" },
  { id: "S-004", name: "Luthfi Pratama" }
];

interface AppUser {
  uid: string;
  email: string | null;
  displayName: string | null;
  role: UserRole;
  salesId?: string;
  salesName?: string;
}

export default function App() {
  const [firebaseUser, setFirebaseUser] = useState<User | null>(null);
  const [appUser, setAppUser] = useState<AppUser | null>(null);
  const [role, setRole] = useState<UserRole>("GM");
  const [selectedSalesId, setSelectedSalesId] = useState<string>("");
  const [loading, setLoading] = useState(true);
  const [currentView, setCurrentView] = useState<View>("Dashboard");
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [isSidebarOpen, setIsSidebarOpen] = useState(true);

  // Real-time Data States
  const [clients, setClients] = useState<CorporateClient[]>([]);
  const [vehicles, setVehicles] = useState<Vehicle[]>([]);
  const [drivers, setDrivers] = useState<Driver[]>([]);
  const [orders, setOrders] = useState<Order[]>([]);
  const [logs, setLogs] = useState<LogEntry[]>([]);
  const [partners, setPartners] = useState<Partner[]>([]);

  useEffect(() => {
    const unsubscribeAuth = onAuthStateChanged(auth, (u) => {
      setFirebaseUser(u);
      setLoading(false);
    });
    return () => unsubscribeAuth();
  }, []);

  useEffect(() => {
    if (!appUser) return;

    const unsubClients = onSnapshot(query(collection(db, "clients"), orderBy("createdAt", "desc")), 
      (snap) => setClients(snap.docs.map(d => ({ id: d.id, ...d.data() } as CorporateClient))),
      (err) => handleFirestoreError(err, OperationType.LIST, "clients")
    );

    const unsubVehicles = onSnapshot(query(collection(db, "vehicles"), orderBy("plateNumber")), 
      (snap) => setVehicles(snap.docs.map(d => ({ id: d.id, ...d.data() } as Vehicle))),
      (err) => handleFirestoreError(err, OperationType.LIST, "vehicles")
    );

    const unsubDrivers = onSnapshot(query(collection(db, "drivers"), orderBy("name")), 
      (snap) => setDrivers(snap.docs.map(d => ({ id: d.id, ...d.data() } as Driver))),
      (err) => handleFirestoreError(err, OperationType.LIST, "drivers")
    );

    // Sales only see their own orders
    const ordersBaseQuery = collection(db, "orders");
    const filteredOrdersQuery = appUser.role === "Sales" && appUser.salesId
      ? query(ordersBaseQuery, where("salesId", "==", appUser.salesId), orderBy("createdAt", "desc"))
      : query(ordersBaseQuery, orderBy("createdAt", "desc"));

    const unsubOrders = onSnapshot(filteredOrdersQuery, 
      (snap) => setOrders(snap.docs.map(d => ({ id: d.id, ...d.data() } as Order))),
      (err) => handleFirestoreError(err, OperationType.LIST, "orders")
    );

    const unsubLogs = onSnapshot(query(collection(db, "logs"), orderBy("timestamp", "desc")), 
      (snap) => setLogs(snap.docs.map(d => ({ id: d.id, ...d.data(), timestamp: d.data().timestamp?.toDate?.() || new Date() } as LogEntry))),
      (err) => handleFirestoreError(err, OperationType.LIST, "logs")
    );

    const unsubPartners = onSnapshot(collection(db, "partners"), 
      (snap) => setPartners(snap.docs.map(d => ({ id: d.id, ...d.data() } as Partner))),
      (err) => handleFirestoreError(err, OperationType.LIST, "partners")
    );

    return () => {
      unsubClients();
      unsubVehicles();
      unsubDrivers();
      unsubOrders();
      unsubLogs();
      unsubPartners();
    };
  }, [appUser]);

  const seedDummyData = async () => {
    console.log("Starting Enterprise Data Seeding...");
    const MOCK_CLIENTS_DATA = [
      { name: "PT. Astra International", email: "procurement@astra.co.id", phone: "+62 21 6522 555", industry: "Automotive", taxId: "01.234.567.8-091.000", address: "Jl. Gaya Motor No. 8, Jakarta", status: "Active" },
      { name: "PT. Telkom Indonesia", email: "b2b@telkom.co.id", phone: "+62 21 5215 111", industry: "Telecommunication", taxId: "02.345.678.9-092.000", address: "Gedung Telkom Landmark Tower, Jakarta", status: "Active" },
      { name: "Shopee Indonesia", email: "fleet@shopee.co.id", phone: "+62 21 8064 7100", industry: "E-commerce", address: "Pacific Century Place, SCBD", status: "Active" },
      { name: "The Ritz-Carlton Jakarta", email: "concierge@ritzcarlton.com", phone: "+62 21 2551 8888", industry: "Hospitality", address: "Mega Kuningan, Jakarta", status: "Active" },
      { name: "Google Indonesia", email: "admin@google.com", industry: "Technology", address: "Pacific Century Place, Jakarta", status: "Active" },
      { name: "PT. Gudang Garam Tbk", email: "logistics@gudanggaramtbk.com", industry: "FMCG", address: "Kediri, Jawa Timur", status: "Active" },
      { name: "Grab Indonesia", email: "fleet.admin@grab.com", industry: "Technology", address: "Gedung Grab, Jakarta", status: "Active" },
      { name: "PT. Pertamina", email: "proc@pertamina.com", industry: "Energy", address: "Jl. Perwira, Jakarta", status: "Active" },
    ];
    
    for (let i = 0; i < MOCK_CLIENTS_DATA.length; i++) {
      const id = `CL-00${i + 1}`;
      console.log(`Seeding client ${id}...`);
      await setDoc(doc(db, "clients", id), {
        ...MOCK_CLIENTS_DATA[i],
        createdAt: serverTimestamp()
      }).catch(e => handleFirestoreError(e, OperationType.WRITE, `clients/${id}`));
    }
    
    const brands = ["Toyota", "Mercedes-Benz", "BMW", "Lexus", "Tesla", "Hino", "Scania", "Isuzu", "Hyundai", "Honda"];
    const models: Record<string, string[]> = {
      "Toyota": ["Alphard", "Camry", "Innova Zenix", "Voxy", "Corolla Cross", "Blind Van"],
      "Mercedes-Benz": ["E 200", "S-Class", "Sprinter", "G-Class"],
      "BMW": ["X5", "5 Series", "7 Series"],
      "Lexus": ["RX", "LM 350", "ES"],
      "Tesla": ["Model 3", "Model Y", "Model X"],
      "Hino": ["Bigbird Bus", "Ranger Logistics", "Dutro"],
      "Scania": ["Intercity Bus", "Cargo Truck"],
      "Isuzu": ["Giga", "Elf", "Traga"],
      "Hyundai": ["Ioniq 5", "Staria", "Palisade"],
      "Honda": ["Accord", "CR-V", "Civic"]
    };
    const categories: Vehicle["category"][] = ["Sedan", "SUV", "Bus", "Luxury Sedan", "Van"];
    const lobs: BusinessLine[] = ["Bluebird", "Silverbird", "Goldenbird", "Big Bird", "BirdMobil", "Ironbird", "Bluebird Kirim", "Cititrans"];
    const statuses: Vehicle["status"][] = ["Available", "Busy", "Maintenance"];

    // Generate 25 vehicles
    for (let i = 0; i < 25; i++) {
      const brand = brands[Math.floor(Math.random() * brands.length)];
      const model = models[brand][Math.floor(Math.random() * models[brand].length)];
      const category = categories[Math.floor(Math.random() * categories.length)];
      const businessLine = lobs[Math.floor(Math.random() * lobs.length)];
      const status = statuses[Math.floor(Math.random() * statuses.length)];
      
      const p2 = Math.floor(1000 + Math.random() * 9000);
      const p3 = String.fromCharCode(65 + Math.floor(Math.random() * 26)) + String.fromCharCode(65 + Math.floor(Math.random() * 26));
      const plate = `B ${p2} ${p3}`;
      const id = `V-${plate.replace(/\s/g, "")}`;
      console.log(`Seeding vehicle ${id}...`);

      await setDoc(doc(db, "vehicles", id), {
        plateNumber: plate,
        stnkNumber: `STNK-${Math.floor(100000 + Math.random() * 900000)}`,
        taxExpiryDate: `2027-0${Math.floor(Math.random() * 9) + 1}-0${Math.floor(Math.random() * 9) + 1}`,
        model: `${brand} ${model}`,
        category,
        businessLine,
        status,
        createdAt: serverTimestamp()
      }).catch(e => handleFirestoreError(e, OperationType.WRITE, `vehicles/${id}`));
    }

    const firstNames = ["Budi", "Agus", "Heri", "Slamet", "Andi", "Dedi", "Anton", "Joko", "Rudi", "Eko", "Iwan", "Sutrisno", "Ahmad", " Bambang", "Riky", "Surya"];
    const lastNames = ["Santoso", "Setiawan", "Wijaya", "Kurniawan", "Susanto", "Saputra", "Pratama", "Hidayat", "Ramadhan", "Gunawan"];

    // Generate 15 drivers
    for (let i = 0; i < 15; i++) {
      const name = `${firstNames[Math.floor(Math.random() * firstNames.length)]} ${lastNames[Math.floor(Math.random() * lastNames.length)]}`;
      const id = `D-DRV-${100 + i}`;
      await setDoc(doc(db, "drivers", id), {
        name,
        phone: `081${Math.floor(10000000 + Math.random() * 90000000)}`,
        licenseNumber: `SIM-${Math.floor(10000000 + Math.random() * 90000000)}`,
        licenseExpiry: "2029-12-31",
        yearsOfExperience: Math.floor(Math.random() * 20) + 2,
        rating: (Math.random() * 1.5 + 3.5).toFixed(1),
        status: "Available",
        createdAt: serverTimestamp()
      }).catch(e => handleFirestoreError(e, OperationType.CREATE, `drivers/${id}`));
    }

    const MOCK_SALES_PEOPLE = [
      { id: "S-001", name: "Andri Setiawan" },
      { id: "S-002", name: "Siska Wijaya" },
      { id: "S-003", name: "Bambang Pamungkas" },
      { id: "S-004", name: "Luthfi Pratama" }
    ];

    // Generate 30 orders
    const statusOptions: Order["status"][] = ["Draft", "Active", "Completed"];
    for (let i = 0; i < 30; i++) {
      const clientId = `CL-00${Math.floor(Math.random() * MOCK_CLIENTS_DATA.length) + 1}`;
      const orderId = `ORD-${2000 + i}`;
      const businessLine = lobs[Math.floor(Math.random() * lobs.length)];
      const price = (Math.floor(Math.random() * 50) + 1) * 1000000;
      
      const pickupDate = new Date();
      pickupDate.setDate(pickupDate.getDate() - Math.floor(Math.random() * 30));
      const returnDate = new Date(pickupDate);
      returnDate.setDate(returnDate.getDate() + Math.floor(Math.random() * 5) + 1);

      const salesPerson = MOCK_SALES_PEOPLE[Math.floor(Math.random() * MOCK_SALES_PEOPLE.length)];

      await setDoc(doc(db, "orders", orderId), {
        clientId,
        vehicleId: `V-B${Math.floor(1000 + Math.random() * 9000)}XX`, 
        driverId: `D-DRV-${100 + Math.floor(Math.random() * 15)}`,
        businessLine,
        status: statusOptions[Math.floor(Math.random() * statusOptions.length)],
        pickupDate: pickupDate.toISOString().split("T")[0],
        returnDate: returnDate.toISOString().split("T")[0],
        price,
        paymentStatus: Math.random() > 0.5 ? "Paid" : "Outstanding",
        salesId: salesPerson.id,
        salesName: salesPerson.name,
        createdAt: serverTimestamp()
      }).catch(e => handleFirestoreError(e, OperationType.CREATE, `orders/${orderId}`));
    }
    
    const MOCK_PARTNERS_EXTRA = [
      { name: "Bengkel Bluebird Pondok Cabe", type: "Maintenance", contactPerson: "Haryono", phone: "021-7400001", email: "maint.pc@bluebird.com", status: "Active" },
      { name: "Pertamina SPBU Central", type: "Fuel", contactPerson: "Sita", phone: "021-3901112", email: "fuel.admin@pertamina.com", status: "Active" },
      { name: "Allianz Corporate", type: "Insurance", contactPerson: "Rudi", phone: "0812-999-000", email: "bluebird.desk@allianz.co.id", status: "Active" },
      { name: "Kalla Logistics", type: "Sub-Contractor", contactPerson: "Yusuf", phone: "0811-4444-555", email: "collaboration@kalla.com", status: "Active" },
    ];
    for (let i = 0; i < MOCK_PARTNERS_EXTRA.length; i++) {
      const id = `PTR-00${i + 1}`;
      await setDoc(doc(db, "partners", id), MOCK_PARTNERS_EXTRA[i])
        .catch(e => handleFirestoreError(e, OperationType.WRITE, `partners/${id}`));
    }
    
    // Add default logs
    const initialLogs: Partial<LogEntry>[] = [
      { id: "L-1", user: "SYSTEM", action: "Environment Seeding", module: "Dashboard", details: "Initial database population completed" },
      { id: "L-2", user: "Budi (GM)", action: "Access View", module: "Logs", details: "Operational audit log access granted." },
      { id: "L-3", user: "Ratna (Finance)", action: "Invoice Processed", module: "Finance", details: "Corporate settlement for AST-101 initiated." }
    ];
    for (const l of initialLogs) {
      await setDoc(doc(db, "logs", l.id!), {
        ...l,
        timestamp: serverTimestamp()
      }).catch(e => handleFirestoreError(e, OperationType.WRITE, `logs/${l.id}`));
    }
    
    alert("Enterprise Data Seeding completed. 25 Vehicles, 15 Drivers, and 30 orders have been generated.");
  };

  if (loading) return <div className="h-screen flex items-center justify-center font-bold text-primary animate-pulse">Initializing Hub...</div>;

  if (!firebaseUser) {
    return (
      <div className="h-screen flex flex-col items-center justify-center bg-accent/10 p-6">
        <div className="max-w-md w-full bg-card p-8 rounded-2xl shadow-xl border text-center space-y-6">
          <div className="w-16 h-16 bg-primary rounded-2xl mx-auto flex items-center justify-center text-white">
            <Car size={32} />
          </div>
          <div>
            <h1 className="text-2xl font-bold tracking-tight">Bluebird B2B Enterprise</h1>
            <p className="text-muted-foreground mt-2">Manage your corporate fleet operations securely.</p>
          </div>
          <Button className="w-full gap-2 h-12" onClick={signIn}>
            <LogIn size={20} />
            Sign in with Google
          </Button>
          <p className="text-[10px] text-muted-foreground uppercase tracking-widest font-bold">Authorized Personnel Only</p>
        </div>
      </div>
    );
  }

  if (!appUser) {
    return (
      <div className="h-screen flex flex-col items-center justify-center bg-accent/10 p-6">
        <div className="max-w-md w-full bg-card p-8 rounded-2xl shadow-xl border space-y-6">
          <div className="text-center">
            <h2 className="text-xl font-bold">Complete Your Profile</h2>
            <p className="text-sm text-muted-foreground">Select your role and account to continue.</p>
          </div>
          
          <div className="space-y-4">
            <div className="space-y-2">
              <Label>Access Role</Label>
              <Select value={role} onValueChange={(v: any) => setRole(v)}>
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="GM">General Manager</SelectItem>
                  <SelectItem value="Finance">Finance</SelectItem>
                  <SelectItem value="Operational">Operational</SelectItem>
                  <SelectItem value="Sales">Sales Executive</SelectItem>
                </SelectContent>
              </Select>
            </div>

            {role === "Sales" && (
              <div className="space-y-2">
                <Label>Select Sales Account</Label>
                <Select value={selectedSalesId} onValueChange={setSelectedSalesId}>
                  <SelectTrigger>
                    <SelectValue placeholder="Identify yourself..." />
                  </SelectTrigger>
                  <SelectContent>
                    {MOCK_SALES_PEOPLE.map(s => (
                      <SelectItem key={s.id} value={s.id}>{s.name}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            )}

            <Button 
              className="w-full" 
              disabled={role === "Sales" && !selectedSalesId}
              onClick={() => {
                const salesPerson = MOCK_SALES_PEOPLE.find(s => s.id === selectedSalesId);
                setAppUser({
                  uid: firebaseUser.uid,
                  email: firebaseUser.email,
                  displayName: firebaseUser.displayName,
                  role: role,
                  salesId: role === "Sales" ? selectedSalesId : undefined,
                  salesName: role === "Sales" ? salesPerson?.name : undefined
                });
              }}
            >
              Enter Hub
            </Button>
            
            <Button variant="ghost" className="w-full text-xs" onClick={() => signOut(auth)}>
              Sign Out
            </Button>
          </div>
        </div>
      </div>
    );
  }

  // Simple Breadcrumb logic
  const getBreadcrumbs = () => {
    const items = [{ label: "Home", view: "Dashboard" as View }];
    if (currentView !== "Dashboard") {
      items.push({ label: currentView, view: currentView });
    }
    if (selectedId) {
      items.push({ label: selectedId, view: currentView });
    }
    return items;
  };

  const navigateTo = (view: View, id: string | null = null) => {
    setCurrentView(view);
    setSelectedId(id);
    window.scrollTo(0, 0);
  };

  return (
    <div className="min-h-screen bg-slate-50 flex font-sans">
      {/* Desktop Sidebar */}
      <aside className={`hidden md:flex flex-col w-60 border-r border-slate-200 bg-white sticky top-0 h-screen transition-all duration-300 ${!isSidebarOpen ? "-ml-60" : ""}`}>
        <div className="p-6 border-b border-slate-200 flex items-center gap-2 text-primary font-bold text-xl tracking-tight">
          <div className="w-8 h-8 bg-primary rounded-md flex items-center justify-center">
            <div className="w-4 h-4 border-2 border-white rotate-45"></div>
          </div>
          Bluebird B2B
        </div>

        <nav className="flex-1 p-4 space-y-1">
          <div className="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Main Console</div>
          <SidebarItem icon={LayoutDashboard} label="GM Command Center" view="Dashboard" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
          <SidebarItem icon={ClipboardList} label="Engagement & Sales" view="Sales" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
          <SidebarItem icon={Car} label="Operational Fleet" view="Fleet" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
          <SidebarItem icon={UserSquare2} label="Operational Staff" view="Drivers" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
          <SidebarItem icon={Users} label="Corporate Partners" view="Clients" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
          <SidebarItem icon={CreditCard} label="Finance & Billing" view="Finance" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />

          <div className="pt-4 px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">System</div>
          <SidebarItem icon={Users} label="Users & Access" view="Users" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
          <SidebarItem icon={Database} label="Master Logs" view="Logs" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
          <SidebarItem icon={UserSquare2} label="Partner Entities" view="Partners" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
          
          <div className="pt-6 mt-6 border-t border-slate-100">
            <div className="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Operational Setup</div>
            <Button 
              variant="outline" 
              size="sm" 
              className="w-full justify-start gap-2 h-9 text-[11px] font-bold border-dashed border-primary/30 text-primary hover:bg-primary/5"
              onClick={seedDummyData}
            >
              <Database size={14} />
              Seed Blueprint Data
            </Button>
          </div>
        </nav>

        <div className="p-4 border-t border-slate-200">
          <div className="flex items-center gap-3 p-2 rounded-lg bg-slate-50 border border-slate-200">
            <div className="w-8 h-8 rounded bg-primary flex items-center justify-center text-[10px] font-bold text-white uppercase">
              {appUser.role.slice(0, 2)}
            </div>
            <div className="flex-1 overflow-hidden">
              <p className="text-xs font-semibold truncate">{appUser.displayName || appUser.email?.split('@')[0] || "User Account"}</p>
              <div className="flex items-center gap-1">
                <p className="text-[10px] text-slate-500 font-bold uppercase">{appUser.role}</p>
                {appUser.salesName && (
                  <>
                    <div className="h-1 w-1 bg-slate-300 rounded-full" />
                    <p className="text-[10px] text-primary font-bold uppercase tracking-tighter">{appUser.salesName}</p>
                  </>
                )}
                <div className="h-1 w-1 bg-slate-300 rounded-full" />
                <Select onValueChange={(val: UserRole) => setAppUser({ ...appUser, role: val })} value={appUser.role}>
                  <SelectTrigger className="h-4 border-none p-0 bg-transparent text-[8px] font-bold text-primary hover:underline">
                    <SelectValue placeholder="Switch" className="text-[8px]" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="GM" className="text-[10px]">General Manager</SelectItem>
                    <SelectItem value="Finance" className="text-[10px]">Finance</SelectItem>
                    <SelectItem value="Operational" className="text-[10px]">Operational</SelectItem>
                    <SelectItem value="Sales" className="text-[10px]">Sales</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </div>
        </div>
      </aside>

      <main className="flex-1 flex flex-col min-w-0 bg-white">
        {/* Header */}
        <header className="h-14 border-b border-slate-200 bg-white sticky top-0 z-30 px-6 flex items-center justify-between">
          <div className="flex items-center gap-4">
            <Button variant="ghost" size="icon" onClick={() => setIsSidebarOpen(!isSidebarOpen)} className="hidden md:flex h-8 w-8">
              <Menu size={16} />
            </Button>
            
            {/* Mobile Nav */}
            <div className="md:hidden">
               <Sheet>
                <SheetTrigger render={<Button variant="ghost" size="icon"><Menu size={16} /></Button>} />
                <SheetContent side="left" className="w-[80%] p-0">
                   <div className="p-6 border-b">
                     <div className="flex items-center gap-2 text-primary font-bold text-xl tracking-tight">
                        <div className="w-8 h-8 bg-primary rounded-md flex items-center justify-center">
                          <div className="w-4 h-4 border-2 border-white rotate-45"></div>
                        </div>
                        Bluebird B2B
                      </div>
                   </div>
                   <nav className="p-4 space-y-1">
                    <div className="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Main Console</div>
                    <SidebarItem icon={LayoutDashboard} label="Dashboard" view="Dashboard" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
                    <SidebarItem icon={ClipboardList} label="Sales & Pricing" view="Sales" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
                    <SidebarItem icon={Car} label="Fleet Operations" view="Fleet" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
                    <SidebarItem icon={UserSquare2} label="Driver Management" view="Drivers" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
                    <SidebarItem icon={Users} label="Corporate Clients" view="Clients" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
                    <SidebarItem icon={CreditCard} label="Finance & Billing" view="Finance" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
                    
                    <div className="pt-4 px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">System</div>
                    <SidebarItem icon={Users} label="Users & Access" view="Users" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
                    <SidebarItem icon={Database} label="Master Logs" view="Logs" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
                    <SidebarItem icon={UserSquare2} label="Partner Entities" view="Partners" currentView={currentView} selectedId={selectedId} role={appUser.role} onNavigate={navigateTo} />
                  </nav>
                </SheetContent>
              </Sheet>
            </div>

            <div className="hidden lg:block">
              <GlobalSearch query={searchQuery} setQuery={setSearchQuery} onSelect={navigateTo} clients={clients} vehicles={vehicles} drivers={drivers} orders={orders} />
            </div>
          </div>

          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" className="relative">
              <Bell size={20} />
              <span className="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-card" />
            </Button>
            <Button variant="ghost" size="icon" onClick={() => { signOut(auth); setAppUser(null); }}>
              <LogOut size={20} className="text-muted-foreground" />
            </Button>
            <div className="w-8 h-8 rounded-full bg-accent border flex items-center justify-center font-bold text-xs">
              {(appUser.displayName?.charAt(0) || appUser.email?.charAt(0) || "U").toUpperCase()}
            </div>
          </div>
        </header>

        {/* Content Area */}
        <div className="p-4 md:p-8 max-w-7xl w-full mx-auto space-y-6 animate-in fade-in duration-500">
          {/* Breadcrumbs */}
          <Breadcrumb>
            <BreadcrumbList className="text-[10px]">
              {getBreadcrumbs().map((bc, i) => (
                <React.Fragment key={i}>
                  <BreadcrumbItem>
                    {i === getBreadcrumbs().length - 1 ? (
                      <BreadcrumbPage className="text-slate-900 font-bold">{bc.label}</BreadcrumbPage>
                    ) : (
                      <BreadcrumbLink onClick={() => navigateTo(bc.view)} className="cursor-pointer hover:text-slate-900 transition-colors">
                        {bc.label}
                      </BreadcrumbLink>
                    )}
                  </BreadcrumbItem>
                  {i < getBreadcrumbs().length - 1 && <BreadcrumbSeparator />}
                </React.Fragment>
              ))}
            </BreadcrumbList>
          </Breadcrumb>

          {/* Views */}
          {currentView === "Dashboard" && <GMDashboard navigateTo={navigateTo} vehicles={vehicles} orders={orders} role={appUser.role} />}
          {currentView === "Clients" && !selectedId && <ClientsList clients={clients} navigateTo={navigateTo} />}
          {currentView === "Fleet" && !selectedId && <FleetList vehicles={vehicles} navigateTo={navigateTo} />}
          {currentView === "Drivers" && !selectedId && <DriversList drivers={drivers} navigateTo={navigateTo} />}
          {currentView === "Sales" && !selectedId && <OrdersList orders={orders} navigateTo={navigateTo} vehicles={vehicles} clients={clients} drivers={drivers} appUser={appUser} />}
          {currentView === "Finance" && !selectedId && <FinanceList orders={orders} clients={clients} navigateTo={navigateTo} partners={partners} />}
          {currentView === "Users" && (
            <div className="flex flex-col items-center justify-center h-[60vh] text-center space-y-4">
              <div className="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                <Users size={32} className="text-slate-400" />
              </div>
              <h3 className="text-lg font-bold">Access Management</h3>
              <p className="text-sm text-slate-500 max-w-xs">User roles and authentication policies are managed through Firebase Console for this enterprise instance.</p>
            </div>
          )}
          {currentView === "Logs" && <LogView logs={logs} onSeed={seedDummyData} />}
          {currentView === "Partners" && <PartnersList partners={partners} />}
          
          {selectedId && currentView === "Clients" && <ClientDetail clientId={selectedId} clients={clients} orders={orders} navigateTo={navigateTo} />}
          {selectedId && currentView === "Fleet" && <VehicleDetail vehicleId={selectedId} vehicles={vehicles} orders={orders} navigateTo={navigateTo} />}
          {selectedId && currentView === "Drivers" && <DriverDetail driverId={selectedId} drivers={drivers} orders={orders} navigateTo={navigateTo} />}
          {selectedId && currentView === "Sales" && <OrderDetail orderId={selectedId} orders={orders} clients={clients} vehicles={vehicles} drivers={drivers} navigateTo={navigateTo} />}
          
          {clients.length === 0 && currentView === "Dashboard" && (
            <div className="p-4 bg-primary/10 rounded-lg flex items-center justify-between">
              <p className="text-sm font-medium">Database is empty. Populate with realistic enterprise data?</p>
              <Button size="sm" onClick={seedDummyData}>Seed Data</Button>
            </div>
          )}

          {/* Detailed Views placeholder removed as they are now implemented above */}
        </div>
      </main>
    </div>
  );
}


function SidebarItem({ icon: Icon, label, view, currentView, selectedId, role, onNavigate }: { icon: any, label: string, view: View, currentView: View, selectedId: string | null, role: UserRole, onNavigate: (v: View) => void }) {
  const isAllowed = ROLE_PERMISSIONS[role].views.includes(view);
  if (!isAllowed) return null;

  return (
    <button
      onClick={() => onNavigate(view)}
      className={`w-full flex items-center gap-3 px-3 py-2 rounded-md transition-all duration-200 border border-transparent ${
        currentView === view && !selectedId
          ? "bg-blue-50 text-primary border-blue-100 shadow-sm" 
          : "text-slate-600 hover:bg-slate-50 hover:text-slate-900"
      }`}
    >
      <Icon size={16} />
      <span className="font-medium text-xs">{label}</span>
    </button>
  );
}

// --- New Detail Components ---

function LogView({ logs, onSeed }: { logs: LogEntry[], onSeed: () => void }) {
  return (
    <Card className="border border-slate-200 rounded-xl shadow-sm overflow-hidden bg-white">
      <CardHeader className="p-4 border-b bg-slate-50/50 flex flex-row items-center justify-between">
        <CardTitle className="text-sm font-bold flex items-center gap-2">
          <Database size={16} />
          System Operational Audit Logs
        </CardTitle>
        <Button variant="outline" size="sm" className="h-8 text-[10px] font-bold uppercase tracking-widest border-slate-200" onClick={onSeed}>
          Seed Enterprise Data (Random)
        </Button>
      </CardHeader>
      <CardContent className="p-0">
        <Table>
          <TableHeader className="bg-slate-50 border-b">
            <TableRow>
              <TableHead className="w-[180px] px-4 py-3 text-[10px] uppercase font-bold">Timestamp</TableHead>
              <TableHead className="w-[120px] px-4 py-3 text-[10px] uppercase font-bold">User Context</TableHead>
              <TableHead className="w-[100px] px-4 py-3 text-[10px] uppercase font-bold">Module</TableHead>
              <TableHead className="px-4 py-3 text-[10px] uppercase font-bold">Action Event</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody className="divide-y divide-slate-100">
            {logs.map((log) => (
              <TableRow key={log.id} className="text-xs">
                <TableCell className="px-4 py-3 font-mono text-slate-500">{log.timestamp.toLocaleString()}</TableCell>
                <TableCell className="px-4 py-3 font-bold text-primary">{log.user}</TableCell>
                <TableCell className="px-4 py-3">
                  <Badge variant="outline" className="text-[9px] uppercase font-bold">{log.module}</Badge>
                </TableCell>
                <TableCell className="px-4 py-3 font-medium text-slate-700">{log.action}</TableCell>
              </TableRow>
            ))}
            {logs.length === 0 && (
              <TableRow>
                <TableCell colSpan={4} className="text-center py-8 text-slate-400 italic">No logs recorded in the current session.</TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  );
}

function ClientDetail({ clientId, clients, orders, navigateTo }: { clientId: string, clients: CorporateClient[], orders: Order[], navigateTo: any }) {
  const client = clients.find(c => c.id === clientId);
  const clientOrders = orders.filter(o => o.clientId === clientId);
  const totalSpend = clientOrders.reduce((sum, o) => sum + o.price, 0);

  if (!client) return <div className="p-8 text-center bg-white rounded-xl border border-dashed">Operational context for <span className="font-mono text-primary">{clientId}</span> not found in synchronizer.</div>;

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4 mb-4">
        <Button variant="ghost" size="sm" onClick={() => navigateTo("Clients")} className="h-8 gap-2 font-bold text-slate-500">
          <ChevronRight size={14} className="rotate-180" /> Back to Partners
        </Button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <Card className="lg:col-span-1 border border-slate-200 rounded-3xl overflow-hidden shadow-lg bg-white">
          <div className="h-32 bg-slate-900 relative">
            <img 
              src={`https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2670&auto=format&fit=crop`} 
              className="w-full h-full object-cover opacity-40 mix-blend-overlay" 
              alt="HQ"
              referrerPolicy="no-referrer"
            />
          </div>
          <CardContent className="p-6 relative -mt-12">
            <div className="w-20 h-20 rounded-2xl bg-white border-4 border-white shadow-2xl flex items-center justify-center text-primary font-black text-3xl mb-4 overflow-hidden">
              <span className="bg-primary w-full h-full flex items-center justify-center text-white">{client.name.charAt(0)}</span>
            </div>
            <h2 className="text-2xl font-black text-slate-900 tracking-tight">{client.name}</h2>
            <p className="text-[10px] font-mono text-slate-400 mb-6 uppercase tracking-widest">{client.id}</p>
            
            <div className="space-y-5 border-t pt-6">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-1">
                  <Label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Industry</Label>
                  <p className="text-xs font-bold text-slate-700">{client.industry || "General Cargo"}</p>
                </div>
                <div className="space-y-1">
                  <Label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">NPWP / Tax ID</Label>
                  <p className="text-xs font-mono font-bold text-slate-600">{client.taxId || "00.000.000.0-000.000"}</p>
                </div>
              </div>

              <div className="space-y-1">
                <Label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Contact Information</Label>
                <div className="space-y-2">
                  <p className="text-sm font-semibold text-primary underline">{client.email}</p>
                  <p className="text-xs font-bold text-slate-600">{client.phone || "+62 21 - N/A"}</p>
                </div>
              </div>
              <div className="space-y-1">
                <Label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Registered Office</Label>
                <div className="flex items-start gap-2">
                   <Database size={14} className="text-slate-400 shrink-0 mt-0.5" />
                   <p className="text-xs text-slate-600 font-medium leading-relaxed">{client.address}</p>
                </div>
              </div>
              <div className="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                 <span className="text-[10px] font-bold text-slate-400 uppercase">Account Integrity</span>
                 <Badge className="bg-emerald-100 text-emerald-700 hover:bg-emerald-100 font-black text-[10px] px-3">{client.status}</Badge>
              </div>
            </div>
          </CardContent>
        </Card>

        <div className="lg:col-span-2 space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
             <Card className="p-6 bg-gradient-to-br from-emerald-50 to-emerald-100 border-none shadow-sm relative overflow-hidden group">
                <TrendingUp className="absolute -right-4 -bottom-4 w-24 h-24 text-emerald-200 group-hover:scale-110 transition-transform" />
                <p className="text-[10px] font-bold text-emerald-600 uppercase mb-1 relative">Lifetime Volume</p>
                <h3 className="text-3xl font-black text-emerald-700 leading-tight relative">Rp {totalSpend.toLocaleString()}</h3>
             </Card>
             <Dialog>
                <DialogTrigger nativeButton={false} render={
                   <Card className="p-6 bg-gradient-to-br from-blue-50 to-blue-100 border-none shadow-sm relative overflow-hidden group cursor-pointer hover:shadow-md transition-all">
                      <Car className="absolute -right-4 -bottom-4 w-24 h-24 text-blue-200 group-hover:scale-110 transition-transform" />
                      <p className="text-[10px] font-bold text-blue-600 uppercase mb-1 relative">Unit Utilization</p>
                      <h3 className="text-3xl font-black text-blue-700 leading-tight relative flex items-center gap-2">
                        {clientOrders.filter(o => o.status === 'Active').length} Units
                        <ChevronRight size={20} className="text-blue-400 group-hover:translate-x-1 transition-transform" />
                      </h3>
                   </Card>
                } />
                <DialogContent className="max-w-md">
                   <DialogHeader>
                      <DialogTitle className="text-lg font-black tracking-tight">Operational Service Distribution</DialogTitle>
                      <DialogDescription>
                         Breakdown of business units actively engaged by <span className="font-bold text-slate-900">{client.name}</span>.
                      </DialogDescription>
                   </DialogHeader>
                   <div className="py-4 space-y-4">
                      {["Bluebird", "Silverbird", "Goldenbird", "Big Bird", "Ironbird", "Cititrans", "Bluebird Kirim", "BirdMobil"].map(lob => {
                         const lobOrders = clientOrders.filter(o => o.businessLine === lob);
                         if (lobOrders.length === 0) return null;
                         return (
                            <div key={lob} className="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                               <div className="flex flex-col">
                                  <span className="text-xs font-black text-slate-800 uppercase tracking-tighter">{lob}</span>
                                  <span className="text-[10px] text-slate-400 font-bold">{lobOrders.length} Recent Contracts</span>
                               </div>
                               <div className="text-right">
                                  <span className="text-sm font-black text-primary">Rp {(lobOrders.reduce((s,o)=>s+o.price,0)/1000000).toFixed(1)}M</span>
                               </div>
                            </div>
                         );
                      })}
                   </div>
                </DialogContent>
             </Dialog>
          </div>

          <Card className="border border-slate-200 rounded-2xl shadow-sm overflow-hidden bg-white">
             <CardHeader className="p-4 bg-slate-50/50 border-b flex flex-row items-center justify-between">
                <CardTitle className="text-sm font-bold">Leasing Pipeline & Service History</CardTitle>
                <Button size="sm" variant="outline" className="h-7 text-[10px] font-bold">Export Statement</Button>
             </CardHeader>
             <CardContent className="p-0">
                <Table>
                  <TableHeader className="bg-slate-50/80">
                    <TableRow>
                      <TableHead className="px-4 py-3 text-[10px] font-bold uppercase">Order #</TableHead>
                      <TableHead className="px-4 py-3 text-[10px] font-bold uppercase">Asset (LOB)</TableHead>
                      <TableHead className="px-4 py-3 text-[10px] font-bold uppercase">Period</TableHead>
                      <TableHead className="px-4 py-3 text-[10px] font-bold uppercase">Valuation</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {clientOrders.map(o => (
                      <TableRow key={o.id} className="text-xs hover:bg-slate-50 transition-colors">
                        <TableCell className="px-4 py-3 font-mono font-bold text-primary">{o.id}</TableCell>
                        <TableCell className="px-4 py-3">
                          <div className="flex flex-col">
                            <span className="font-bold text-slate-800 underline cursor-pointer" onClick={() => navigateTo("Fleet", o.vehicleId)}>{o.vehicleId}</span>
                            <span className="text-[9px] font-black text-primary italic uppercase tracking-tighter">{o.businessLine}</span>
                          </div>
                        </TableCell>
                        <TableCell className="px-4 py-3 text-slate-500">{o.pickupDate}</TableCell>
                        <TableCell className="px-4 py-3 font-black text-slate-900">Rp {(o.price/1000000).toFixed(1)}M</TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
             </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}

function VehicleDetail({ vehicleId, vehicles, orders, navigateTo }: { vehicleId: string, vehicles: Vehicle[], orders: Order[], navigateTo: any }) {
  const vehicle = vehicles.find(v => v.id === vehicleId);
  const vehicleOrders = orders.filter(o => o.vehicleId === vehicleId);

  if (!vehicle) return <div className="p-8 text-center bg-white rounded-xl border border-dashed">Asset identifier <span className="font-mono text-primary">{vehicleId}</span> not found in operational registry.</div>;

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4 mb-4">
        <Button variant="ghost" size="sm" onClick={() => navigateTo("Fleet")} className="h-8 gap-2 font-bold text-slate-500">
          <ChevronRight size={14} className="rotate-180" /> Back to Registry
        </Button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div className="lg:col-span-1 space-y-6">
          <Card className="border border-slate-200 rounded-3xl shadow-xl overflow-hidden bg-white">
            <div className="h-48 bg-slate-900 relative">
               <img 
                src={vehicle.category === 'Bus' 
                  ? "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=2600&auto=format&fit=crop" 
                  : "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?q=80&w=2670&auto=format&fit=crop"} 
                className="w-full h-full object-cover opacity-60" 
                alt="Vehicle"
                referrerPolicy="no-referrer"
               />
               <div className="absolute top-4 left-4 flex flex-col gap-2">
                  <Badge className="bg-primary text-white font-black text-xs px-3 py-1 scale-110 shadow-lg border-2 border-white/20">{vehicle.plateNumber}</Badge>
                  <Badge variant="outline" className="bg-white/90 text-primary border-none font-black text-[9px] uppercase tracking-widest">{vehicle.businessLine}</Badge>
               </div>
            </div>
            <CardContent className="p-6">
               <h2 className="text-2xl font-black text-slate-900 mb-1">{vehicle.model}</h2>
               <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-6">Unit Asset ID: {vehicle.id}</p>
               
               <div className="space-y-4 border-t pt-6">
                  <div className="flex items-center justify-between">
                    <span className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Operational State</span>
                    <Badge variant={vehicle.status === 'Available' ? 'default' : 'secondary'} className="font-black text-[10px] uppercase">
                      {vehicle.status}
                    </Badge>
                  </div>

                  <div className="space-y-4 pt-2">
                    <div className="space-y-1">
                      <Label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">STNK Registration</Label>
                      <p className="text-xs font-mono font-bold text-slate-700">{vehicle.stnkNumber || "SN-1029384756"}</p>
                    </div>
                    <div className="space-y-1">
                      <Label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tax Expiry Synchronization</Label>
                      <p className={`text-xs font-bold ${new Date(vehicle.taxExpiryDate || "2026-12-31") < new Date() ? 'text-red-500' : 'text-emerald-600'}`}>
                        {vehicle.taxExpiryDate || "2026-12-31"}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-center justify-between pt-4 border-t">
                    <span className="text-[10px] font-bold text-slate-400 uppercase">Internal Category</span>
                    <span className="text-xs font-black text-primary italic uppercase tracking-tighter">{vehicle.category}</span>
                  </div>
               </div>

               <div className="mt-8 space-y-2">
                 <MaintenanceDialog vehicle={vehicle} onUpdate={() => {}} />
                 <Button variant="outline" className="w-full text-xs font-bold h-9">Regulatory Audit</Button>
               </div>
            </CardContent>
          </Card>
        </div>

        <div className="lg:col-span-3 space-y-6">
           <Card className="border border-slate-200 rounded-3xl shadow-sm bg-white overflow-hidden">
             <CardHeader className="p-6 bg-slate-50/80 border-b">
                <CardTitle className="text-sm font-bold flex items-center gap-2">
                  <ClipboardList size={16} /> Asset Utilization Audit Log
                </CardTitle>
             </CardHeader>
             <CardContent className="p-0">
                <Table>
                  <TableHeader className="bg-slate-50/50">
                    <TableRow>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Contract Ref</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Deployment Date</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Corp Partner</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Assigned Personnel</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Status</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase text-right">Revenue</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {vehicleOrders.map(o => (
                      <TableRow key={o.id} className="text-xs hover:bg-slate-50 group">
                        <TableCell className="px-6 py-4 font-mono font-bold text-primary">{o.id}</TableCell>
                        <TableCell className="px-6 py-4 font-medium text-slate-500">{o.pickupDate} — {o.returnDate}</TableCell>
                        <TableCell 
                          className="px-6 py-4 font-black text-slate-900 group-hover:text-primary cursor-pointer transition-colors"
                          onClick={() => navigateTo("Clients", o.clientId)}
                        >
                          {o.clientId}
                        </TableCell>
                        <TableCell 
                          className="px-6 py-4 font-medium text-slate-600 underline cursor-pointer"
                          onClick={() => navigateTo("Drivers", o.driverId)}
                        >
                          {o.driverId}
                        </TableCell>
                        <TableCell className="px-6 py-4">
                           <Badge variant={o.status === "Completed" ? "outline" : "default"} className={`text-[9px] font-black uppercase ${o.status === 'Completed' ? 'text-emerald-600 border-emerald-200 bg-emerald-50' : ''}`}>
                              {o.status}
                           </Badge>
                        </TableCell>
                        <TableCell className="px-6 py-4 font-black text-right">Rp {o.price.toLocaleString()}</TableCell>
                      </TableRow>
                    ))}
                    {vehicleOrders.length === 0 && (
                      <TableRow>
                        <TableCell colSpan={5} className="text-center py-10 text-slate-400 italic">Zero utilization footprint found for this unit.</TableCell>
                      </TableRow>
                    )}
                  </TableBody>
                </Table>
             </CardContent>
           </Card>
        </div>
      </div>
    </div>
  );
}

function DriverDetail({ driverId, drivers, orders, navigateTo }: { driverId: string, drivers: Driver[], orders: Order[], navigateTo: any }) {
  const driver = drivers.find(d => d.id === driverId);
  const driverOrders = orders.filter(o => o.driverId === driverId);

  if (!driver) return <div className="p-8 text-center bg-white rounded-xl border border-dashed">Personnel record <span className="font-mono text-primary">{driverId}</span> not localized in global HR database.</div>;

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4 mb-4">
        <Button variant="ghost" size="sm" onClick={() => navigateTo("Drivers")} className="h-8 gap-2 text-slate-500 font-bold">
          <ChevronRight size={14} className="rotate-180" /> Back to HR Desk
        </Button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div className="lg:col-span-1 space-y-6">
          <Card className="border border-slate-200 rounded-3xl shadow-xl overflow-hidden bg-white">
            <div className="h-32 bg-primary relative flex items-center justify-center">
               <div className="w-20 h-20 rounded-full border-4 border-white bg-white overflow-hidden shadow-2xl relative translate-y-12">
                  <img 
                    src={`https://images.unsplash.com/photo-1599566150163-29194dcaad36?q=80&w=2574&auto=format&fit=crop`} 
                    className="w-full h-full object-cover" 
                    alt="Driver"
                    referrerPolicy="no-referrer"
                  />
               </div>
               <div className="absolute top-4 right-4 bg-white/20 px-2 py-1 rounded-full text-white font-black text-[10px] flex items-center gap-1">
                  <TrendingUp size={10} /> {driver.rating || 4.9}
               </div>
            </div>
            <CardContent className="pt-16 p-6">
               <div className="text-center">
                  <h2 className="text-2xl font-black text-slate-900 leading-tight">{driver.name}</h2>
                  <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 mb-6">Staff Seniority: {driver.yearsOfExperience || 10} Years</p>
               </div>
               
               <div className="space-y-4 border-t pt-6">
                  <div className="grid grid-cols-1 gap-4">
                    <div className="space-y-1">
                      <Label className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Personal Identification / License</Label>
                      <p className="text-xs font-mono font-bold text-slate-700">{driver.licenseNumber || "IDN-1029-3847-56"}</p>
                      <p className="text-[9px] font-bold text-emerald-600 uppercase tracking-widest">Valid Thru: {driver.licenseExpiry || "2028-12-12"}</p>
                    </div>
                  </div>

                  <div className="pt-2">
                    <Label className="text-[10px] font-bold text-slate-400 uppercase">Direct Contact</Label>
                    <p className="text-sm font-semibold text-primary">{driver.phone}</p>
                  </div>

                  <div className="flex items-center justify-between pt-4 border-t">
                    <span className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</span>
                    <Badge variant={driver.status === 'Available' ? 'default' : 'secondary'} className="font-black text-[10px] uppercase">
                      {driver.status}
                    </Badge>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Current Unit</span>
                    <span className="text-xs font-mono font-bold text-slate-700">{driver.assignedVehicleId || "STANDBY"}</span>
                  </div>
               </div>

               <div className="mt-8 flex flex-col gap-2">
                 <Button className="w-full text-xs font-bold h-9">Service Performance Review</Button>
               </div>
            </CardContent>
          </Card>
        </div>

        <div className="lg:col-span-3">
           <Card className="border border-slate-200 rounded-3xl shadow-sm overflow-hidden bg-white">
             <CardHeader className="p-6 bg-slate-50 border-b">
                <CardTitle className="text-sm font-bold flex items-center gap-2">
                  <Clock size={16} /> Operational Mission Archive
                </CardTitle>
             </CardHeader>
             <CardContent className="p-0">
                <Table>
                  <TableHeader>
                    <TableRow className="bg-slate-50/50 hover:bg-transparent">
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Order Identity</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Mission Schedule</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">LOB Segment</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Corp Logistics Partner</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Unit</TableHead>
                      <TableHead className="px-6 py-4 text-[10px] font-bold uppercase">Outcome</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {driverOrders.map(o => (
                      <TableRow key={o.id} className="text-xs hover:bg-slate-50 transition-colors group">
                        <TableCell className="px-6 py-4 font-mono font-bold text-primary">{o.id}</TableCell>
                        <TableCell className="px-6 py-4 font-medium text-slate-500 whitespace-nowrap">{o.pickupDate} — {o.returnDate}</TableCell>
                        <TableCell className="px-6 py-4">
                          <Badge variant="outline" className="text-[9px] font-black uppercase text-primary border-primary/20">{o.businessLine}</Badge>
                        </TableCell>
                        <TableCell 
                          className="px-6 py-4 font-black text-slate-900 group-hover:text-primary transition-colors cursor-pointer"
                          onClick={() => navigateTo("Clients", o.clientId)}
                        >
                          {o.clientId}
                        </TableCell>
                        <TableCell 
                          className="px-6 py-4 font-medium text-slate-700 underline cursor-pointer"
                          onClick={() => navigateTo("Fleet", o.vehicleId)}
                        >
                          {o.vehicleId}
                        </TableCell>
                        <TableCell className="px-6 py-4">
                           <Badge variant={o.status === "Completed" ? "outline" : "default"} className={`text-[9px] font-black uppercase ${o.status === 'Completed' ? 'text-emerald-600 border-emerald-200 bg-emerald-50' : ''}`}>
                              {o.status}
                           </Badge>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
             </CardContent>
           </Card>
        </div>
      </div>
    </div>
  );
}

function PartnersList({ partners }: { partners: Partner[] }) {
  return (
    <Card className="border border-slate-200 rounded-xl shadow-sm overflow-hidden bg-white">
      <CardHeader className="p-4 border-b bg-slate-50/50">
        <div className="flex items-center justify-between">
          <CardTitle className="text-sm font-bold flex items-center gap-2">
            <UserSquare2 size={16} /> Operational Partner Entities & Vendors
          </CardTitle>
          <Button size="sm" className="h-8 text-[10px] font-bold">+ Onboard Vendor</Button>
        </div>
      </CardHeader>
      <CardContent className="p-0">
        <Table>
          <TableHeader className="bg-slate-50">
            <TableRow>
              <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500">Partner Entity</TableHead>
              <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500">Service Category</TableHead>
              <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500">Main Contact</TableHead>
              <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 text-right">Integrations</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody className="divide-y divide-slate-100">
            {partners.map((partner) => (
              <TableRow key={partner.id} className="hover:bg-slate-50 transition-colors">
                <TableCell className="px-4 py-3">
                  <div className="flex flex-col">
                    <span className="text-xs font-black text-slate-900 tracking-tight">{partner.name}</span>
                    <span className="text-[10px] font-mono text-slate-400 uppercase">{partner.id}</span>
                  </div>
                </TableCell>
                <TableCell className="px-4 py-3">
                  <Badge variant="secondary" className="text-[9px] font-bold uppercase tracking-widest">{partner.type}</Badge>
                </TableCell>
                <TableCell className="px-4 py-3">
                  <div className="flex flex-col">
                    <span className="text-xs font-bold text-slate-700">{partner.contactPerson}</span>
                    <span className="text-[10px] text-slate-400">{partner.email}</span>
                  </div>
                </TableCell>
                <TableCell className="px-4 py-3 text-right">
                  <Button variant="ghost" size="sm" className="h-7 text-[10px] text-primary font-bold">Manage Contracts</Button>
                </TableCell>
              </TableRow>
            ))}
            {partners.length === 0 && (
              <TableRow>
                <TableCell colSpan={4} className="text-center py-10 text-slate-400 italic">No external logistics or maintenance partners synchronized yet.</TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  );
}

function OrderDetail({ orderId, orders, vehicles, clients, drivers, navigateTo }: { 
  orderId: string, 
  orders: Order[], 
  vehicles: Vehicle[],
  clients: CorporateClient[],
  drivers: Driver[],
  navigateTo: any 
}) {
  const order = orders.find(o => o.id === orderId);
  if (!order) return <div>Order not found.</div>;
  const client = clients.find(c => c.id === order.clientId);
  const vehicle = vehicles.find(v => v.id === order.vehicleId);
  const driver = drivers.find(d => d.id === order.driverId);

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4 mb-4">
        <Button variant="ghost" size="sm" onClick={() => navigateTo("Sales")} className="h-8 gap-2 font-bold text-slate-500">
          <X size={14} /> Back to Pipeline
        </Button>
      </div>

      <div className="max-w-4xl mx-auto space-y-6">
        <div className="flex items-center justify-between">
           <div>
              <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Service Engagement</p>
              <h2 className="text-3xl font-black text-slate-900 tracking-tight">{order.id}</h2>
           </div>
           <Badge className="bg-primary hover:bg-primary font-black text-xs px-4 py-1.5 uppercase shadow-lg shadow-primary/20">{order.status}</Badge>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
           <Card className="border border-slate-200 rounded-3xl p-6 space-y-6 bg-white shadow-sm">
              <div className="space-y-4">
                <div className="space-y-1">
                  <Label className="text-[10px] font-bold text-slate-400 uppercase">Operational Period</Label>
                  <div className="flex items-center gap-2 text-sm font-bold text-slate-700">
                    <Clock size={14} className="text-primary" />
                    <span>{order.pickupDate} — {order.returnDate}</span>
                  </div>
                </div>
                <div className="space-y-1">
                  <Label className="text-[10px] font-bold text-slate-400 uppercase">Engagement Value</Label>
                  <div className="text-2xl font-black text-emerald-600">Rp {order.price.toLocaleString()}</div>
                </div>
                <div className="space-y-1">
                  <Label className="text-[10px] font-bold text-slate-400 uppercase">Settlement Sync</Label>
                  <Badge variant={order.paymentStatus === 'Paid' ? 'outline' : 'secondary'} className={`font-black uppercase text-[10px] ${order.paymentStatus === 'Paid' ? 'text-emerald-600 border-emerald-200' : 'text-amber-600'}`}>
                    {order.paymentStatus}
                  </Badge>
                </div>
              </div>
           </Card>

           <div className="space-y-4">
              <Card 
                className="border border-slate-200 rounded-2xl p-4 flex items-center justify-between hover:border-primary/40 transition-colors cursor-pointer group bg-white"
                onClick={() => navigateTo("Clients", order.clientId)}
              >
                 <div className="flex items-center gap-4">
                    <div className="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-primary font-bold">{client?.name.charAt(0)}</div>
                    <div>
                       <p className="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Partner Client</p>
                       <p className="text-sm font-black text-slate-900 group-hover:text-primary transition-colors">{client?.name}</p>
                    </div>
                 </div>
                 <ChevronRight size={16} className="text-slate-300 group-hover:text-primary" />
              </Card>

              <Card 
                className="border border-slate-200 rounded-2xl p-4 flex items-center justify-between hover:border-primary/40 transition-colors cursor-pointer group bg-white"
                onClick={() => navigateTo("Fleet", order.vehicleId)}
              >
                 <div className="flex items-center gap-4">
                    <div className="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 font-mono font-bold text-xs">{vehicle?.plateNumber.split(' ')[1]}</div>
                    <div>
                       <p className="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Leased Asset</p>
                       <p className="text-sm font-black text-slate-900 group-hover:text-primary transition-colors">{vehicle?.model} ({vehicle?.plateNumber})</p>
                    </div>
                 </div>
                 <ChevronRight size={16} className="text-slate-300 group-hover:text-primary" />
              </Card>

              <Card 
                className="border border-slate-200 rounded-2xl p-4 flex items-center justify-between hover:border-primary/40 transition-colors cursor-pointer group bg-white"
                onClick={() => navigateTo("Drivers", order.driverId)}
              >
                 <div className="flex items-center gap-4">
                    <div className="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-700 font-bold">{driver?.name.charAt(0)}</div>
                    <div>
                       <p className="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Assigned Personnel</p>
                       <p className="text-sm font-black text-slate-900 group-hover:text-primary transition-colors">{driver?.name}</p>
                    </div>
                 </div>
                 <ChevronRight size={16} className="text-slate-300 group-hover:text-primary" />
              </Card>
           </div>
        </div>

        <div className="pt-8 flex gap-3">
           <Button className="font-bold flex-1 h-12 shadow-lg shadow-primary/20">Finalize Dispatch Protocol</Button>
           <Button variant="outline" className="font-bold h-12 px-8 border-slate-200">Modify Engagement</Button>
        </div>
      </div>
    </div>
  );
}

function CreateOrderDialog({ vehicles, clients, drivers, onCreate, appUser }: { 
  vehicles: Vehicle[], 
  clients: CorporateClient[], 
  drivers: Driver[],
  onCreate: (order: Order) => void,
  appUser: AppUser
}) {
  const [open, setOpen] = useState(false);
  const [pickupDate, setPickupDate] = useState("");
  const [returnDate, setReturnDate] = useState("");
  const [selectedVehicle, setSelectedVehicle] = useState("");
  const [selectedClient, setSelectedClient] = useState("");
  const [selectedDriver, setSelectedDriver] = useState("");
  const [selectedSales, setSelectedSales] = useState(appUser.role === "Sales" ? appUser.salesId || "" : "");
  const [businessLine, setBusinessLine] = useState<BusinessLine>("Bluebird");
  const [price, setPrice] = useState("");

  const formatIDR = (val: string) => {
    if (!val) return "";
    const number = val.replace(/\D/g, "");
    return number.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  };

  const parseIDR = (val: string) => {
    return val.replace(/\./g, "");
  };

  // ... rest of state stays same but uses selectedSales correctly in handleCreate
  const availableVehicles = vehicles.filter(v => {
    if (v.status === "Maintenance") return false;
    return v.status === "Available";
  });
  
  const handleCreate = async () => {
    try {
      const orderId = `ORD-${Math.floor(Math.random() * 9000) + 1000}`;
      const salesId = appUser.role === "Sales" ? appUser.salesId : selectedSales;
      const salesPerson = MOCK_SALES_PEOPLE.find(s => s.id === salesId);
      await setDoc(doc(db, "orders", orderId), {
        clientId: selectedClient,
        vehicleId: selectedVehicle,
        driverId: selectedDriver,
        salesId: salesId,
        salesName: salesPerson?.name || "Unassigned",
        status: "Draft",
        pickupDate,
        returnDate,
        price: Number(price),
        paymentStatus: "Outstanding",
        createdAt: serverTimestamp()
      });
      // Update vehicle status too
      await updateDoc(doc(db, "vehicles", selectedVehicle), { status: "Busy" });
      setOpen(false); // Close dialog on success
      // Reset form
      setPrice(""); setSelectedVehicle(""); setSelectedClient(""); setSelectedDriver("");
    } catch (e) {
      handleFirestoreError(e, OperationType.CREATE, "orders");
    }
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button size="sm" onClick={() => setOpen(true)} className="h-8 text-[10px] font-black uppercase tracking-widest">+ New Operational Order</Button>} />
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>B2B Engagement Setup</DialogTitle>
          <DialogDescription>
            Dispatch a new operational order to a corporate partner.
          </DialogDescription>
        </DialogHeader>
        <div className="grid gap-4 py-4">
          <div className="grid grid-cols-2 gap-4 text-xs">
            <div className="grid gap-1.5">
              <Label className="text-[10px] font-bold uppercase text-slate-400">Partner Legal Entity</Label>
              <Select onValueChange={setSelectedClient}>
                <SelectTrigger className="h-9">
                  <SelectValue placeholder="Select" />
                </SelectTrigger>
                <SelectContent>
                  {clients.map(c => <SelectItem key={c.id} value={c.id}>{c.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="grid gap-1.5">
              <Label className="text-[10px] font-bold uppercase text-slate-400">Service Line (LOB)</Label>
              <Select onValueChange={(val: any) => setBusinessLine(val)} value={businessLine}>
                <SelectTrigger className="h-9">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Bluebird">Bluebird (Taxi)</SelectItem>
                  <SelectItem value="Silverbird">Silverbird (Premium)</SelectItem>
                  <SelectItem value="Goldenbird">Goldenbird (Car Rental)</SelectItem>
                  <SelectItem value="Big Bird">Big Bird (Bus)</SelectItem>
                  <SelectItem value="Ironbird">Ironbird (Logistics & Cargo)</SelectItem>
                  <SelectItem value="Cititrans">Cititrans (Executive Shuttle)</SelectItem>
                  <SelectItem value="Bluebird Kirim">Bluebird Kirim (Delivery)</SelectItem>
                  <SelectItem value="BirdMobil">BirdMobil (Mobility Ecosystem)</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="grid gap-1.5">
              <Label className="text-[10px] font-bold uppercase text-slate-400">Start window</Label>
              <Input type="date" className="h-9" value={pickupDate} onChange={e => setPickupDate(e.target.value)} />
            </div>
            <div className="grid gap-1.5">
              <Label className="text-[10px] font-bold uppercase text-slate-400">Completion</Label>
              <Input type="date" className="h-9" value={returnDate} onChange={e => setReturnDate(e.target.value)} />
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="grid gap-1.5">
              <Label className="text-[10px] font-bold uppercase text-slate-400">Available Asset</Label>
              <Select onValueChange={setSelectedVehicle}>
                <SelectTrigger className="h-9">
                  <SelectValue placeholder="Unit" />
                </SelectTrigger>
                <SelectContent>
                  {availableVehicles.map(v => (
                    <SelectItem key={v.id} value={v.id}>{v.model} ({v.plateNumber})</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="grid gap-1.5">
              <Label className="text-[10px] font-bold uppercase text-slate-400">Dedicated Staff</Label>
              <Select onValueChange={setSelectedDriver}>
                <SelectTrigger className="h-9">
                  <SelectValue placeholder="Staff" />
                </SelectTrigger>
                <SelectContent>
                  {drivers.filter(d => d.status === 'Available').map(d => (
                    <SelectItem key={d.id} value={d.id}>{d.name}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>
          {appUser.role !== "Sales" && (
            <div className="grid gap-1.5">
              <Label className="text-[10px] font-bold uppercase text-slate-400">Sales Executive</Label>
              <Select value={selectedSales} onValueChange={setSelectedSales}>
                <SelectTrigger className="h-9">
                  <SelectValue placeholder="Select Account Executive" />
                </SelectTrigger>
                <SelectContent>
                  {MOCK_SALES_PEOPLE.map(s => (
                    <SelectItem key={s.id} value={s.id}>{s.name}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          )}
          {appUser.role === "Sales" && (
            <div className="grid gap-1.5">
              <Label className="text-[10px] font-bold uppercase text-slate-400">Sales Executive</Label>
              <div className="h-9 px-3 flex items-center bg-slate-50 border rounded-md text-xs font-semibold text-slate-600">
                {appUser.salesName}
              </div>
            </div>
          )}
          <div className="grid gap-1.5">
            <Label className="text-[10px] font-bold uppercase text-slate-400">Engagement Valuation (IDR)</Label>
            <Input 
              type="text" 
              placeholder="e.g. 1.500.000" 
              value={formatIDR(price)} 
              onChange={e => setPrice(parseIDR(e.target.value))}
              className="h-9 font-black text-primary border-primary/20"
            />
          </div>
        </div>
        <DialogFooter>
          <Button onClick={handleCreate} className="w-full font-black uppercase tracking-widest" disabled={!selectedVehicle || !selectedClient || !price}>Authorize & Dispatch</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

function MaintenanceDialog({ vehicle, onUpdate }: { vehicle: Vehicle, onUpdate: any }) {
  const [open, setOpen] = useState(false);
  const [estDate, setEstDate] = useState("");
  const [serviceType, setServiceType] = useState("regular");

  const handleMaintenance = async () => {
    try {
      await updateDoc(doc(db, "vehicles", vehicle.id), {
        status: "Maintenance",
        estimatedCompletionDate: estDate
      });
      setOpen(false); // Close dialog on success
    } catch (e) {
      handleFirestoreError(e, OperationType.UPDATE, `vehicles/${vehicle.id}`);
    }
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button variant="outline" size="sm" onClick={() => setOpen(true)} className="h-7 text-[10px] text-red-600 border-red-200 hover:bg-red-50">Set Maintenance</Button>} />
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Vehicle Maintenance Schedule</DialogTitle>
          <DialogDescription>
            Setting <span className="font-mono font-bold">{vehicle.plateNumber}</span> to maintenance. This will hide it from active availability.
          </DialogDescription>
        </DialogHeader>
        <div className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="service">Service Type</Label>
            <Select onValueChange={setServiceType} value={serviceType}>
              <SelectTrigger>
                <SelectValue placeholder="Select service type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="regular">Regular Checkup</SelectItem>
                <SelectItem value="repair">Mechanical Repair</SelectItem>
                <SelectItem value="body">Body & Paint</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div className="grid gap-2">
            <Label htmlFor="est">Estimated Completion Date (Required)</Label>
            <Input type="date" value={estDate} onChange={e => setEstDate(e.target.value)} />
          </div>
        </div>
        <DialogFooter>
          <Button disabled={!estDate} variant="destructive" onClick={handleMaintenance}>Confirm Maintenance Status</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

const GlobalSearch = ({ query: searchQ, setQuery, onSelect, clients, vehicles, drivers, orders }: { 
  query: string, 
  setQuery: any, 
  onSelect: any,
  clients: CorporateClient[],
  vehicles: Vehicle[],
  drivers: Driver[],
  orders: Order[]
}) => {
  return (
    <div className="relative">
      <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" size={16} />
      <Input 
        placeholder="Global search (Plate, ID, Client)..." 
        className="pl-10 w-[300px] lg:w-[450px] h-9 bg-accent/30 border-none focus-visible:ring-1 focus-visible:ring-primary/30"
        value={searchQ}
        onChange={(e) => setQuery(e.target.value)}
      />
      {searchQ.length > 1 && (
        <div className="absolute top-full left-0 right-0 mt-2 bg-card border rounded-lg shadow-xl z-50 overflow-hidden max-h-[300px] overflow-y-auto">
          <div className="p-2 text-[10px] font-bold text-muted-foreground uppercase tracking-widest bg-accent/20">Search Results</div>
          {[...clients, ...vehicles, ...drivers, ...orders].filter(item => {
            const str = JSON.stringify(item).toLowerCase();
            return str.includes(searchQ.toLowerCase());
          }).map((item: any) => (
             <button 
              key={item.id} 
              className="w-full text-left p-3 hover:bg-accent border-b last:border-b-0 flex items-center justify-between group"
              onClick={() => {
                const typeMap: any = { 'CL': 'Clients', 'V': 'Fleet', 'D': 'Drivers', 'ORD': 'Sales' };
                const prefix = item.id.split('-')[0];
                onSelect(typeMap[prefix] || 'Dashboard', item.id);
                setQuery("");
              }}
             >
               <div>
                  <p className="text-xs font-bold">{item.name || item.plateNumber || item.id}</p>
                  <p className="text-[10px] text-muted-foreground">{item.id}</p>
               </div>
               <ChevronRight size={14} className="text-muted-foreground group-hover:text-primary transition-colors" />
             </button>
          ))}
        </div>
      )}
    </div>
  );
};

// --- Revenue Chart Component ---

function RevenueChart({ orders, onDetailClick }: { orders: Order[], onDetailClick: () => void }) {
  const [timeframe, setTimeframe] = useState<"day" | "week" | "month" | "year">("day");
  
  const getChartData = () => {
    const now = new Date();
    let data: { name: string, total: number }[] = [];

    if (timeframe === "day") {
      const last7Days = eachDayOfInterval({
        start: subDays(now, 6),
        end: now
      });
      data = last7Days.map(date => {
        const total = orders
          .filter(o => isSameDay(new Date(o.pickupDate), date))
          .reduce((sum, o) => sum + o.price, 0);
        return { name: format(date, "EEE"), total };
      });
    } else if (timeframe === "week") {
      const last4Weeks = eachWeekOfInterval({
        start: subWeeks(now, 3),
        end: now
      });
      data = last4Weeks.map((date, i) => {
        const total = orders
          .filter(o => isSameWeek(new Date(o.pickupDate), date))
          .reduce((sum, o) => sum + o.price, 0);
        return { name: `Week ${i + 1}`, total };
      });
    } else if (timeframe === "month") {
      const last6Months = eachMonthOfInterval({
        start: subMonths(now, 5),
        end: now
      });
      data = last6Months.map(date => {
        const total = orders
          .filter(o => isSameMonth(new Date(o.pickupDate), date))
          .reduce((sum, o) => sum + o.price, 0);
        return { name: format(date, "MMM"), total };
      });
    } else if (timeframe === "year") {
      const years = [subYears(now, 1), now];
      data = years.map(date => {
        const total = orders
          .filter(o => isSameYear(new Date(o.pickupDate), date))
          .reduce((sum, o) => sum + o.price, 0);
        return { name: format(date, "yyyy"), total };
      });
    }

    return data;
  };

  const chartData = getChartData();
  const currentTotal = chartData.reduce((sum, d) => sum + d.total, 0);
  const prevTotal = timeframe === "day" ? 
    orders.filter(o => isSameDay(new Date(o.pickupDate), subDays(new Date(), 7))).reduce((s,o)=>s+o.price,0) : 0;
  
  const percentageChange = prevTotal > 0 ? ((currentTotal - prevTotal) / prevTotal * 100).toFixed(1) : "+12.5";

  return (
    <Card className="border border-slate-200 rounded-3xl overflow-hidden bg-white shadow-xl">
      <CardHeader className="p-6 border-b flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2 mb-1">
            <TrendingUp size={16} className="text-emerald-500" />
            <CardTitle className="text-lg font-black tracking-tight">Revenue Stream Analysis</CardTitle>
          </div>
          <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest leading-none">
            Consolidated B2B Ledger Activity
          </p>
        </div>

        <Tabs defaultValue="day" className="w-full md:w-fit" onValueChange={(v: any) => setTimeframe(v)}>
          <TabsList className="bg-slate-100 h-9 p-1">
            <TabsTrigger value="day" className="text-[10px] font-bold uppercase px-3">Hari</TabsTrigger>
            <TabsTrigger value="week" className="text-[10px] font-bold uppercase px-3">Minggu</TabsTrigger>
            <TabsTrigger value="month" className="text-[10px] font-bold uppercase px-3">Bulan</TabsTrigger>
            <TabsTrigger value="year" className="text-[10px] font-bold uppercase px-3">Tahun</TabsTrigger>
          </TabsList>
        </Tabs>
      </CardHeader>
      
      <CardContent className="p-6">
        <div className="flex items-end gap-6 mb-8">
           <div>
              <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Estimated Total ({timeframe})</p>
              <h2 className="text-3xl font-black text-slate-900 tracking-tighter">
                IDR {(currentTotal / 1000000).toFixed(1)}M
              </h2>
           </div>
           <div className={`flex items-center gap-1 mb-1 px-2 py-0.5 rounded-full text-[10px] font-bold ${Number(percentageChange) >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'}`}>
              {Number(percentageChange) >= 0 ? <ArrowUpRight size={12} /> : <ArrowDownRight size={12} />}
              {Math.abs(Number(percentageChange))}%
           </div>
        </div>

        <div className="h-[250px] w-full flex items-center justify-center bg-slate-50 border border-dashed rounded-xl overflow-hidden p-4">
          <BarChart width={500} height={200} data={chartData}>
            <CartesianGrid strokeDasharray="3 3" vertical={false} />
            <XAxis dataKey="name" />
            <YAxis />
            <Tooltip />
            <Bar dataKey="total" fill="#2563eb" radius={[4, 4, 0, 0]} />
          </BarChart>
        </div>
        
        <div className="mt-6 flex items-center justify-between">
           <div className="flex gap-4">
              <div className="flex items-center gap-2">
                 <div className="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(37,99,235,0.4)]" />
                 <span className="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Enterprise Load</span>
              </div>
              <div className="flex items-center gap-2">
                 <div className="w-2 h-2 rounded-full bg-slate-200" />
                 <span className="text-[10px] font-bold text-slate-400 uppercase tracking-tight">Market Avg</span>
              </div>
           </div>
           <Button variant="ghost" className="h-8 gap-2 text-[10px] font-black uppercase text-primary hover:bg-blue-50" onClick={onDetailClick}>
              Explore Ledger Details
              <ChevronRight size={14} />
           </Button>
        </div>
      </CardContent>
    </Card>
  );
}

function SalesPerformance({ orders }: { orders: Order[] }) {
  const salesData = useMemo(() => {
    const data: Record<string, { total: number, count: number }> = {};
    orders.forEach(o => {
      const name = o.salesName || "Unassigned";
      if (!data[name]) data[name] = { total: 0, count: 0 };
      data[name].total += o.price;
      data[name].count += 1;
    });
    return Object.entries(data).map(([name, stats]) => ({
      name,
      ...stats
    })).sort((a, b) => b.total - a.total);
  }, [orders]);

  return (
    <Card className="border border-slate-200 rounded-xl shadow-sm h-full bg-white overflow-hidden">
      <CardHeader className="p-4 border-b bg-slate-50/50">
        <CardTitle className="text-xs font-bold flex items-center justify-between uppercase tracking-wider">
          <span>Target Progress by Consultant</span>
          <TrendingUp size={14} className="text-emerald-500" />
        </CardTitle>
      </CardHeader>
      <CardContent className="p-0">
        <div className="p-4 h-[250px]">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={salesData} layout="vertical" margin={{ left: -20, right: 10 }}>
              <CartesianGrid strokeDasharray="3 3" horizontal={true} vertical={false} stroke="#f1f5f9" />
              <XAxis type="number" hide />
              <YAxis 
                type="category" 
                dataKey="name" 
                tick={{ fontSize: 9, fontWeight: 'bold', fill: '#64748b' }} 
                width={80}
                axisLine={false}
                tickLine={false}
              />
              <Tooltip 
                formatter={(val: any) => [`IDR ${(val / 1000000).toFixed(1)}M`, 'Revenue']}
                contentStyle={{ borderRadius: '8px', fontSize: '10px', fontWeight: 'bold' }}
              />
              <Bar dataKey="total" fill="#2563eb" radius={[0, 4, 4, 0]} barSize={16} />
            </BarChart>
          </ResponsiveContainer>
        </div>
        <div className="border-t divide-y divide-slate-100 max-h-[300px] overflow-y-auto">
          {salesData.map((s, i) => (
             <div key={i} className="px-4 py-3 flex items-center justify-between text-[10px] font-bold transition-colors hover:bg-slate-50">
                <div className="flex flex-col">
                  <span className="text-slate-900">{s.name}</span>
                  <span className="text-slate-400 font-medium">{s.count} Engagements</span>
                </div>
                <div className="text-right">
                  <span className="text-primary block font-black underline decoration-primary/20">IDR {(s.total / 1000000).toFixed(1)}M</span>
                  <span className="text-[8px] text-emerald-600 uppercase">On Target</span>
                </div>
             </div>
          ))}
        </div>
      </CardContent>
    </Card>
  );
}

function GMDashboard({ navigateTo, vehicles, orders, role }: { 
  navigateTo: (v: View, id?: string | null) => void,
  vehicles: Vehicle[],
  orders: Order[],
  role: UserRole
}) {
  const activeOrders = orders.filter(o => o.status === 'Active').length;
  const utilization = vehicles.length > 0 ? Math.round((vehicles.filter(v => v.status === 'Busy').length / vehicles.length) * 100) : 0;
  const maintenanceCount = vehicles.filter(v => v.status === 'Maintenance').length;
  const projectedRevenue = orders.reduce((acc, curr) => acc + curr.price, 0);

  const stats = [
    { label: "Fleet Utilization", value: `${utilization}%`, icon: Car, trend: "+2.4%", color: "text-blue-600", bg: "bg-blue-50", navigate: "Fleet" as View },
    { label: "Revenue Projection", value: `IDR ${(projectedRevenue / 1000000).toFixed(1)}M`, icon: TrendingUp, trend: "+12%", color: "text-emerald-600", bg: "bg-emerald-50", navigate: "Finance" as View },
    { label: "Maintenance Alerts", value: `${maintenanceCount}`, icon: AlertCircle, trend: maintenanceCount > 2 ? "Overdue" : "Stable", color: "text-red-600", bg: "bg-red-50", navigate: "Fleet" as View },
    { label: "Active Orders", value: `${activeOrders}`, icon: ClipboardList, trend: "On Track", color: "text-indigo-600", bg: "bg-indigo-50", navigate: "Sales" as View },
  ];

  return (
    <div className="space-y-6">
      {/* Hero Banner */}
      <div className="relative h-48 md:h-64 rounded-3xl overflow-hidden bg-primary shadow-2xl group">
        <img 
           src="https://images.unsplash.com/photo-1449333206010-8547432a9341?q=80&w=2670&auto=format&fit=crop" 
           className="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay group-hover:scale-105 transition-transform duration-1000" 
           alt="Fleet"
           referrerPolicy="no-referrer"
        />
        <div className="absolute inset-0 bg-gradient-to-tr from-primary via-primary/60 to-transparent" />
        <div className="relative h-full flex flex-col justify-center px-8 md:px-12 space-y-2">
          <Badge className="w-fit bg-blue-400/20 text-white border-blue-400/30 font-bold tracking-widest text-[10px] uppercase">Enterprise HQ</Badge>
          <h1 className="text-3xl md:text-5xl font-black text-white tracking-tight leading-none drop-shadow-sm">
            {role === 'GM' ? 'Command Central' : `${role} Operations`}
          </h1>
          <p className="text-blue-100 text-sm md:text-lg font-medium max-w-xl opacity-90">
            Real-time synchronization for Indonesia's premium B2B logistics network.
          </p>
        </div>
      </div>

      <div className="flex items-center justify-between">
        <h2 className="text-xl font-bold tracking-tight text-slate-900 font-heading">Performance Snapshot</h2>
        {ROLE_PERMISSIONS[role].views.includes("Sales") && (
          <Button className="bg-primary text-white hover:bg-blue-800 h-9 font-bold text-xs" onClick={() => navigateTo("Sales")}>
            + Create New Order
          </Button>
        )}
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((stat, i) => {
          const isAllowed = ROLE_PERMISSIONS[role].views.includes(stat.navigate);
          return (
            <button 
              key={i} 
              disabled={!isAllowed}
              onClick={() => navigateTo(stat.navigate)}
              className={`p-4 border border-slate-200 text-left rounded-xl bg-white shadow-sm shadow-black/[0.02] transition-all relative overflow-hidden group ${isAllowed ? 'hover:border-primary/50 hover:shadow-md cursor-pointer active:scale-[0.98]' : 'opacity-70 grayscale cursor-not-allowed'} ${stat.trend === 'Overdue' ? 'border-l-4 border-l-red-500 bg-red-50/10' : ''}`}
            >
               <div className="flex items-start justify-between mb-2">
                 <div className={`p-1.5 rounded-lg ${stat.bg}`}>
                   <stat.icon size={16} className={stat.color} />
                 </div>
                 <span className={`text-[10px] px-1.5 py-0.5 rounded-full font-bold ${
                    stat.trend === 'Overdue' ? 'bg-red-100 text-red-700' : 
                    stat.trend.includes('+') ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-primary'
                   }`}>
                     {stat.trend}
                  </span>
               </div>
               <p className="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">{stat.label}</p>
               <h3 className={`text-2xl font-black ${stat.trend === 'Overdue' ? 'text-red-600' : 'text-slate-900'}`}>{stat.value}</h3>
               {isAllowed && (
                 <div className="mt-4 flex items-center gap-1 text-[8px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity uppercase tracking-widest">
                    <span>Jump to Module</span>
                    <ChevronRight size={10} />
                 </div>
               )}
            </button>
          );
        })}
      </div>

      <Card className="border border-slate-200 rounded-3xl bg-white shadow-sm overflow-hidden border-t-4 border-t-primary/20">
         <CardHeader className="p-4 border-b bg-slate-50/50 flex flex-row items-center gap-2">
            <AlertCircle size={16} className="text-primary" />
            <CardTitle className="text-sm font-black italic uppercase tracking-wider">Strategic Integrated Scopes</CardTitle>
         </CardHeader>
         <CardContent className="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {[
              { title: "BirdMobil", desc: "Layanan mobil terpercaya: beli, perawatan, jual, dan inspeksi – secara transparan & digital." },
              { title: "Ironbird", desc: "Layanan logistik dan pengiriman kargo skala besar untuk kebutuhan operasional enterprise." },
              { title: "Bluebird Kirim", desc: "Logistik dalam kota dengan tarif transparan dan sistem pelacakan real-time presisi." },
              { title: "Cititrans", desc: "Executive inter-city shuttle. Armada premium dengan kenyamanan maksimal & jadwal fleksibel." }
            ].map((scope, idx) => (
              <div key={idx} className="space-y-2 group">
                 <h4 className="text-xs font-black text-primary uppercase tracking-tighter border-b border-primary/10 pb-1 w-fit group-hover:border-primary transition-colors">{scope.title}</h4>
                 <p className="text-[10px] text-slate-600 leading-relaxed font-semibold italic">{scope.desc}</p>
              </div>
            ))}
         </CardContent>
      </Card>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          <RevenueChart orders={orders} onDetailClick={() => navigateTo("Finance")} />
          
          <Card className="border border-slate-200 rounded-xl shadow-sm">
            <CardHeader className="p-4 border-b">
              <CardTitle className="text-sm font-bold">Recent Logistics Movements</CardTitle>
            </CardHeader>
          <CardContent className="p-0">
             <Table>
                <TableHeader className="bg-slate-50 border-b">
                  <TableRow className="hover:bg-transparent border-none">
                    <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500">Order ID</TableHead>
                    <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500">Client</TableHead>
                    <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500">Vehicle</TableHead>
                    <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500">Status</TableHead>
                  </TableRow>
                </TableHeader>
              <TableBody className="divide-y divide-slate-100">
                {orders.slice(0, 5).map(o => {
                  const canSeeSales = ROLE_PERMISSIONS[role].views.includes("Sales");
                  const canSeeClients = ROLE_PERMISSIONS[role].views.includes("Clients");
                  return (
                    <TableRow key={o.id} className="hover:bg-slate-50 border-none transition-colors">
                      <TableCell 
                        className={`px-4 py-3 text-xs font-mono font-bold ${canSeeSales ? 'text-primary hover:underline cursor-pointer' : 'text-slate-400 font-normal'}`} 
                        onClick={() => canSeeSales && navigateTo("Sales", o.id)}
                      >
                        {o.id}
                      </TableCell>
                      <TableCell 
                        className={`px-4 py-3 text-xs font-semibold ${canSeeClients ? 'text-primary hover:underline cursor-pointer' : 'text-slate-600'}`} 
                        onClick={() => canSeeClients && navigateTo("Clients", o.clientId)}
                      >
                        {o.clientId}
                      </TableCell>
                      <TableCell className="px-4 py-3 text-xs font-bold text-slate-700">
                        <span className="bg-slate-100 px-1.5 py-0.5 rounded">{o.vehicleId}</span>
                      </TableCell>
                      <TableCell className="px-4 py-3">
                        <Badge variant="outline" className="text-[10px] font-bold rounded-full bg-blue-50 text-primary border-primary/20 uppercase tracking-tighter">{o.status}</Badge>
                      </TableCell>
                    </TableRow>
                  );
                })}
              </TableBody>
           </Table>
        </CardContent>
      </Card>
    </div>

        <div className="space-y-6">
          <SalesPerformance orders={orders} />
          
          <Card className="border border-slate-200 rounded-xl shadow-sm bg-white">
            <CardHeader className="p-4 border-b">
              <CardTitle className="text-sm font-bold">Maintenance Queue</CardTitle>
            </CardHeader>
          <CardContent className="p-4">
            <div className="space-y-4">
              {vehicles.filter(v => v.status === 'Maintenance').slice(0, 4).map((v) => (
                <div key={v.id} className="flex items-center justify-between p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                  <div className="flex items-center gap-3">
                    <Badge variant="outline" className="font-mono text-[10px] bg-slate-100 text-slate-700 border-none px-2">{v.plateNumber}</Badge>
                    <div>
                      <p className="text-xs font-bold text-slate-900">{v.model}</p>
                      <p className="text-[10px] text-slate-500">Due: {v.estimatedCompletionDate || 'TBD'}</p>
                    </div>
                  </div>
                  <AlertCircle size={14} className="text-red-500" />
                </div>
              ))}
              {maintenanceCount === 0 && <p className="text-xs text-center py-4 text-muted-foreground italic">No units in maintenance.</p>}
              {ROLE_PERMISSIONS[role].views.includes("Fleet") && (
                <Button variant="outline" className="w-full text-xs font-bold border-slate-200 h-8" onClick={() => navigateTo("Fleet")}>View Full Fleet Data</Button>
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
  );
}

function CreateClientDialog() {
  const [open, setOpen] = useState(false);
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [address, setAddress] = useState("");

  const handleCreate = async () => {
    try {
      const clientId = `CL-${Math.floor(Math.random() * 900) + 100}`;
      await setDoc(doc(db, "clients", clientId), {
        name,
        email,
        address,
        status: "Active",
        createdAt: serverTimestamp()
      });
      setName(""); setEmail(""); setAddress("");
      setOpen(false); // Close dialog on success
    } catch (e) {
      handleFirestoreError(e, OperationType.CREATE, "clients");
    }
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button size="sm" onClick={() => setOpen(true)} className="h-8 text-xs font-bold">+ New Partner</Button>} />
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Register Corporate Partner</DialogTitle>
          <DialogDescription>Add a new B2B entity to the enterprise hub.</DialogDescription>
        </DialogHeader>
        <div className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="name">Corporation Name</Label>
            <Input id="name" value={name} onChange={e => setName(e.target.value)} placeholder="e.g. PT. Global Tech" />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="email">Contract Email</Label>
            <Input id="email" type="email" value={email} onChange={e => setEmail(e.target.value)} placeholder="procurement@company.com" />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="address">Headquarters Address</Label>
            <Input id="address" value={address} onChange={e => setAddress(e.target.value)} />
          </div>
        </div>
        <DialogFooter>
          <Button onClick={handleCreate} disabled={!name || !email}>Establish Partnership</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

function AddVehicleDialog() {
  const [open, setOpen] = useState(false);
  const [plate, setPlate] = useState("");
  const [model, setModel] = useState("");
  const [category, setCategory] = useState<"Sedan" | "SUV" | "Bus" | "Luxury Sedan" | "Van">("Sedan");
  const [businessLine, setBusinessLine] = useState<BusinessLine>("Bluebird");

  const handleCreate = async () => {
    try {
      const vehicleId = `V-${plate.replace(/\s/g, '').toUpperCase()}`;

      await setDoc(doc(db, "vehicles", vehicleId), {
        plateNumber: plate.toUpperCase(),
        stnkNumber: "STNK-" + Math.random().toString(36).substring(7).toUpperCase(),
        taxExpiryDate: "2027-01-01",
        model,
        category,
        businessLine,
        status: "Available",
        createdAt: serverTimestamp()
      });
      setPlate(""); setModel("");
      setOpen(false); // Close dialog on success
    } catch (e) {
      handleFirestoreError(e, OperationType.CREATE, "vehicles");
    }
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button size="sm" onClick={() => setOpen(true)} className="h-8 text-xs font-bold">+ Unit Induction</Button>} />
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Fleet Asset Induction</DialogTitle>
          <DialogDescription>Register a new vehicle into the operational fleet.</DialogDescription>
        </DialogHeader>
        <div className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="plate">Plate Number</Label>
            <Input id="plate" value={plate} onChange={e => setPlate(e.target.value)} placeholder="B 1234 XYZ" />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="model">Vehicle Model</Label>
            <Input id="model" value={model} onChange={e => setModel(e.target.value)} placeholder="e.g. Toyota Alphard" />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="grid gap-2">
              <Label htmlFor="category">Asset Class</Label>
              <Select onValueChange={(val: any) => setCategory(val)} value={category}>
                <SelectTrigger>
                  <SelectValue placeholder="Select Class" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Sedan">Sedan</SelectItem>
                  <SelectItem value="Luxury Sedan">Luxury Sedan</SelectItem>
                  <SelectItem value="SUV">SUV</SelectItem>
                  <SelectItem value="Van">Van</SelectItem>
                  <SelectItem value="Bus">Bus</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="grid gap-2">
              <Label htmlFor="businessLine">Business Line</Label>
              <Select onValueChange={(val: any) => setBusinessLine(val)} value={businessLine}>
                <SelectTrigger>
                  <SelectValue placeholder="Select LOB" />
                </SelectTrigger>
                <SelectContent>
                  {["Bluebird", "Silverbird", "Goldenbird", "Big Bird", "Ironbird", "Cititrans", "Bluebird Kirim", "BirdMobil"].map(lob => (
                    <SelectItem key={lob} value={lob}>{lob}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>
        <DialogFooter>
          <Button onClick={handleCreate} disabled={!plate || !model}>Induct Unit</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

function ClientsList({ clients, navigateTo }: { clients: CorporateClient[], navigateTo: any }) {
  const [sortKey, setSortKey] = useState<string>("name");
  const [sortOrder, setSortOrder] = useState<"asc" | "desc">("asc");

  const toggleSort = (key: string) => {
    if (sortKey === key) {
      setSortOrder(sortOrder === "asc" ? "desc" : "asc");
    } else {
      setSortKey(key);
      setSortOrder("asc");
    }
  };

  const sortedClients = useMemo(() => {
    return [...clients].sort((a, b) => {
      let aValue: any = a[sortKey as keyof CorporateClient] || "";
      let bValue: any = b[sortKey as keyof CorporateClient] || "";
      
      if (typeof aValue === 'string') aValue = aValue.toLowerCase();
      if (typeof bValue === 'string') bValue = bValue.toLowerCase();

      if (aValue < bValue) return sortOrder === "asc" ? -1 : 1;
      if (aValue > bValue) return sortOrder === "asc" ? 1 : -1;
      return 0;
    });
  }, [clients, sortKey, sortOrder]);

  const SortIcon = ({ column }: { column: string }) => {
    if (sortKey !== column) return <ArrowUpDown size={12} className="ml-1 text-slate-300" />;
    return sortOrder === "asc" ? <ArrowUp size={12} className="ml-1 text-primary" /> : <ArrowDown size={12} className="ml-1 text-primary" />;
  };

  return (
    <Card className="border border-slate-200 rounded-xl shadow-sm overflow-hidden">
      <CardHeader className="p-4 border-b flex flex-row items-center justify-between bg-white">
        <div>
          <CardTitle className="text-sm font-bold">Enterprise Corporate Partners</CardTitle>
        </div>
        <CreateClientDialog />
      </CardHeader>
      <CardContent className="p-0">
        <Table>
          <TableHeader className="bg-slate-50 border-b">
            <TableRow className="hover:bg-transparent border-none">
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("name")}
              >
                <div className="flex items-center">Partner Legal Identity <SortIcon column="name" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("email")}
              >
                <div className="flex items-center">Contact Control <SortIcon column="email" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("address")}
              >
                <div className="flex items-center">Regional HQ <SortIcon column="address" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("status")}
              >
                <div className="flex items-center">Sync Status <SortIcon column="status" /></div>
              </TableHead>
              <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 text-right">Action</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody className="divide-y divide-slate-100">
            {sortedClients.map((client) => (
              <TableRow key={client.id} className="hover:bg-slate-50 border-none transition-colors">
                <TableCell className="px-4 py-3">
                  <button onClick={() => navigateTo("Clients", client.id)} className="font-bold text-sm text-primary hover:underline block truncate max-w-[200px]">
                    {client.name}
                  </button>
                  <p className="text-[10px] font-mono text-slate-400 mt-0.5">{client.id}</p>
                </TableCell>
                <TableCell className="px-4 py-3">
                  <div className="flex flex-col text-xs text-slate-600">
                    <span className="font-medium">Primary: {client.email}</span>
                  </div>
                </TableCell>
                <TableCell className="px-4 py-3 text-xs text-slate-600 font-medium">{client.address}</TableCell>
                <TableCell className="px-4 py-3">
                  <Badge variant={client.status === "Active" ? "default" : "secondary"} className="text-[10px] font-bold rounded-full px-2 py-0">
                    {client.status.toUpperCase()}
                  </Badge>
                </TableCell>
                <TableCell className="px-4 py-3 text-right">
                  <Button variant="outline" className="h-7 px-2 text-[10px] font-bold border-slate-200" onClick={() => navigateTo("Clients", client.id)}>Partner Hub</Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  );
}function FleetList({ vehicles, navigateTo }: { vehicles: Vehicle[], navigateTo: any }) {
  const [lobFilter, setLobFilter] = useState<BusinessLine | "All">("All");
  const [categoryFilter, setCategoryFilter] = useState<Vehicle["category"] | "All">("All");
  const [searchTerm, setSearchTerm] = useState("");
  const [sortKey, setSortKey] = useState<string>("plateNumber");
  const [sortOrder, setSortOrder] = useState<"asc" | "desc">("asc");

  const toggleSort = (key: string) => {
    if (sortKey === key) {
      setSortOrder(sortOrder === "asc" ? "desc" : "asc");
    } else {
      setSortKey(key);
      setSortOrder("asc");
    }
  };

  const SortIcon = ({ column }: { column: string }) => {
    if (sortKey !== column) return <ArrowUpDown size={12} className="ml-1 text-slate-300" />;
    return sortOrder === "asc" ? <ArrowUp size={12} className="ml-1 text-primary" /> : <ArrowDown size={12} className="ml-1 text-primary" />;
  };
  
  const filteredVehicles = useMemo(() => {
    let result = vehicles
      .filter(v => {
        const lobMatch = lobFilter === "All" || (v.businessLine && v.businessLine.toLowerCase().trim() === lobFilter.toLowerCase().trim());
        const categoryMatch = categoryFilter === "All" || (v.category && v.category.toLowerCase().trim() === categoryFilter.toLowerCase().trim());
        const plateMatch = !searchTerm || v.plateNumber?.toLowerCase().includes(searchTerm.toLowerCase());
        const modelMatch = !searchTerm || v.model?.toLowerCase().includes(searchTerm.toLowerCase());
        return lobMatch && categoryMatch && (plateMatch || modelMatch);
      });

    return result.sort((a, b) => {
      let aValue: any = a[sortKey as keyof Vehicle] || "";
      let bValue: any = b[sortKey as keyof Vehicle] || "";
      
      if (typeof aValue === 'string') aValue = aValue.toLowerCase();
      if (typeof bValue === 'string') bValue = bValue.toLowerCase();

      if (aValue < bValue) return sortOrder === "asc" ? -1 : 1;
      if (aValue > bValue) return sortOrder === "asc" ? 1 : -1;
      return 0;
    });
  }, [vehicles, lobFilter, categoryFilter, searchTerm, sortKey, sortOrder]);

  return (
    <Card className="border border-slate-200 rounded-xl shadow-sm overflow-hidden bg-white">
      <CardHeader className="p-4 border-b space-y-4">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div className="flex flex-col md:flex-row items-start md:items-center gap-4">
            <CardTitle className="text-sm font-bold">Active Fleet Registry</CardTitle>
            <div className="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-full w-full md:w-auto">
              <Search size={14} className="text-slate-400" />
              <input 
                type="text" 
                placeholder="Search Plate Number..." 
                className="bg-transparent border-none outline-none text-[10px] font-bold text-slate-700 w-full md:w-32 placeholder:text-slate-400"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>
          </div>
          <AddVehicleDialog />
        </div>

        <div className="space-y-3">
          <div className="flex flex-wrap items-center gap-2">
            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-widest min-w-[70px]">Business Line:</span>
            <div className="flex gap-1 overflow-x-auto pb-1 scrollbar-hide">
              {["All", "Bluebird", "Silverbird", "Goldenbird", "Big Bird", "Ironbird", "Cititrans", "Bluebird Kirim", "BirdMobil"].map((l) => (
                <Button 
                  key={l}
                  variant={lobFilter === l ? "default" : "outline"} 
                  className="h-6 px-2 text-[8px] font-black uppercase tracking-widest rounded-full shrink-0"
                  onClick={() => setLobFilter(l as any)}
                >
                  {l}
                </Button>
              ))}
            </div>
          </div>

          <div className="flex flex-wrap items-center gap-2">
            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-widest min-w-[70px]">Asset Class:</span>
            <div className="flex gap-1 overflow-x-auto pb-1 scrollbar-hide">
              {["All", "Sedan", "SUV", "Bus", "Luxury Sedan", "Van"].map((c) => (
                <Button 
                  key={c}
                  variant={categoryFilter === c ? "default" : "outline"} 
                  className="h-6 px-2 text-[8px] font-black uppercase tracking-widest rounded-full shrink-0"
                  onClick={() => setCategoryFilter(c as any)}
                >
                  {c}
                </Button>
              ))}
            </div>
          </div>
        </div>
      </CardHeader>
      <CardContent className="p-0">
        <Table>
          <TableHeader className="bg-slate-50 border-b">
            <TableRow className="hover:bg-transparent border-none">
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("plateNumber")}
              >
                <div className="flex items-center">Reg. Plate & LOB <SortIcon column="plateNumber" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("model")}
              >
                <div className="flex items-center">Unit Model <SortIcon column="model" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("category")}
              >
                <div className="flex items-center">Asset Class <SortIcon column="category" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("status")}
              >
                <div className="flex items-center">Operational State <SortIcon column="status" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("taxExpiryDate")}
              >
                <div className="flex items-center">Tax Expiry <SortIcon column="taxExpiryDate" /></div>
              </TableHead>
              <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 text-right">Operations</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody className="divide-y divide-slate-100">
            {filteredVehicles.length > 0 ? (
              filteredVehicles.map((vehicle) => (
                <TableRow key={vehicle.id} className="hover:bg-slate-50 border-none transition-colors">
                  <TableCell className="px-4 py-3">
                    <div className="flex flex-col gap-1">
                      <button onClick={() => navigateTo("Fleet", vehicle.id)} className="font-mono text-sm font-black text-primary hover:underline bg-slate-100 px-2 py-0.5 rounded tracking-tighter w-fit">
                        {vehicle.plateNumber}
                      </button>
                      <span className="text-[8px] font-black text-slate-400 uppercase tracking-tighter italic">{vehicle.businessLine || "Unknown LOB"}</span>
                    </div>
                  </TableCell>
                  <TableCell className="px-4 py-3 text-xs font-bold text-slate-900">{vehicle.model}</TableCell>
                  <TableCell className="px-4 py-3">
                    <Badge variant="outline" className="text-[10px] font-bold border-slate-200 text-slate-500 px-1.5 py-0">{vehicle.category?.toUpperCase() || "ASSET"}</Badge>
                  </TableCell>
                  <TableCell className="px-4 py-3">
                    <div className="flex items-center gap-2">
                      <span className={`w-2 h-2 rounded-full ${
                        vehicle.status === 'Available' ? 'bg-emerald-500' : 
                        vehicle.status === 'Maintenance' ? 'bg-red-500' : 'bg-blue-500'
                      }`} />
                      <span className="text-[10px] font-bold uppercase tracking-tight text-slate-700">{vehicle.status}</span>
                    </div>
                  </TableCell>
                  <TableCell className="px-4 py-3 text-[10px] font-mono text-slate-500">
                    {vehicle.taxExpiryDate || "2026-12-31"}
                  </TableCell>
                  <TableCell className="px-4 py-3 text-right flex items-center justify-end gap-2">
                     <MaintenanceDialog vehicle={vehicle} onUpdate={() => {}} />
                     <Button variant="outline" className="h-7 px-2 text-[10px] font-bold border-slate-200" onClick={() => navigateTo("Fleet", vehicle.id)}>Details</Button>
                  </TableCell>
                </TableRow>
              ))
            ) : (
              <TableRow>
                <TableCell colSpan={6} className="h-32 text-center bg-slate-50/30">
                  <div className="flex flex-col items-center justify-center gap-2 text-slate-400">
                    <Car size={32} className="opacity-20" />
                    <div className="space-y-1">
                      <p className="text-[10px] font-bold uppercase tracking-widest text-slate-500">No assets found</p>
                      <p className="text-[9px] font-medium text-slate-400">Try adjusting your filters or search terms</p>
                    </div>
                    <Button variant="outline" size="sm" className="h-7 px-3 mt-2 text-[9px] font-bold uppercase tracking-widest rounded-full" onClick={() => { setLobFilter("All"); setCategoryFilter("All"); setSearchTerm(""); }}>
                      Reset Filters
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  );
}

function AddDriverDialog() {
  const [open, setOpen] = useState(false);
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [license, setLicense] = useState("");
  const [experience, setExperience] = useState("");

  const handleCreate = async () => {
    try {
      const driverId = `D-${Math.floor(Math.random() * 900) + 100}`;
      await setDoc(doc(db, "drivers", driverId), {
        name,
        phone,
        licenseNumber: license,
        licenseExpiry: "2029-01-01",
        yearsOfExperience: parseInt(experience) || 5,
        rating: 5.0,
        status: "Available",
        createdAt: serverTimestamp()
      });
      setName(""); setPhone(""); setLicense(""); setExperience("");
      setOpen(false); // Close dialog on success
    } catch (e) {
      handleFirestoreError(e, OperationType.CREATE, "drivers");
    }
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button size="sm" onClick={() => setOpen(true)} className="h-8 text-xs font-bold">+ Enroll Personnel</Button>} />
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Personnel Certification & Enrollment</DialogTitle>
          <DialogDescription>Add new certified personnel to the enterprise pool.</DialogDescription>
        </DialogHeader>
        <div className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="dname">Staff Full Name</Label>
            <Input id="dname" value={name} onChange={e => setName(e.target.value)} placeholder="e.g. Budi Santoso" />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="phone">Contact Channel</Label>
            <Input id="phone" value={phone} onChange={e => setPhone(e.target.value)} placeholder="0812..." />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="grid gap-2">
              <Label htmlFor="license">Driving License #</Label>
              <Input id="license" value={license} onChange={e => setLicense(e.target.value)} placeholder="1234-..." />
            </div>
            <div className="grid gap-2">
              <Label htmlFor="exp">Exp (Years)</Label>
              <Input id="exp" type="number" value={experience} onChange={e => setExperience(e.target.value)} placeholder="5" />
            </div>
          </div>
        </div>
        <DialogFooter>
          <Button onClick={handleCreate} disabled={!name || !phone}>Authorize Personnel</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

function DriversList({ drivers, navigateTo }: { drivers: Driver[], navigateTo: any }) {
  const [searchTerm, setSearchTerm] = useState("");
  const [statusFilter, setStatusFilter] = useState<Driver["status"] | "All">("All");
  
  const filteredDrivers = useMemo(() => {
    return drivers.filter(d => {
      const nameMatch = d.name.toLowerCase().includes(searchTerm.toLowerCase()) || d.id.toLowerCase().includes(searchTerm.toLowerCase());
      const statusMatch = statusFilter === "All" || d.status === statusFilter;
      return nameMatch && statusMatch;
    });
  }, [drivers, searchTerm, statusFilter]);

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div className="flex flex-col md:flex-row items-start md:items-center gap-4">
          <h2 className="text-sm font-bold text-slate-900 px-1 italic">Certified Operational Personnel Registry</h2>
          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2 bg-white border border-slate-200 px-3 py-1.5 rounded-full w-full md:w-64 shadow-sm">
              <Search size={14} className="text-slate-400" />
              <input 
                type="text" 
                placeholder="Search personnel by name or ID..." 
                className="bg-transparent border-none outline-none text-[10px] font-bold text-slate-700 w-full placeholder:text-slate-400"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>
            <div className="flex gap-1">
              {["All", "Available", "Busy", "Off"].map((s) => (
                <Button 
                  key={s}
                  variant={statusFilter === s ? "default" : "outline"} 
                  className={`h-6 px-2 text-[8px] font-black uppercase tracking-widest rounded-full ${
                    statusFilter === s ? "" : 
                    s === 'Available' ? 'text-emerald-600 border-emerald-100 hover:bg-emerald-50' :
                    s === 'Busy' ? 'text-blue-600 border-blue-100 hover:bg-blue-50' : 
                    'text-slate-500 border-slate-100 hover:bg-slate-50'
                  }`}
                  onClick={() => setStatusFilter(s as any)}
                >
                  {s}
                </Button>
              ))}
            </div>
          </div>
        </div>
        <AddDriverDialog />
      </div>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredDrivers.map((driver) => (
          <Card key={driver.id} className="border border-slate-200 rounded-3xl shadow-sm hover:shadow-xl transition-all group overflow-hidden bg-white border-t-4 border-t-primary/10">
            <CardHeader className="pb-2">
              <div className="flex justify-between items-start">
                <div className="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-primary font-black text-lg overflow-hidden group-hover:scale-105 transition-transform shadow-inner">
                   {driver.name.charAt(0)}
                </div>
                <div className="flex flex-col items-end gap-1">
                  <Badge variant={driver.status === 'Available' ? 'default' : 'secondary'} className="text-[9px] font-black uppercase rounded-full px-2">
                    {driver.status}
                  </Badge>
                  <div className="flex items-center gap-1 text-amber-500 text-[10px] font-black">
                    <TrendingUp size={10} /> {driver.rating || 5.0}
                  </div>
                </div>
              </div>
              <CardTitle className="mt-4 text-lg font-black text-slate-900 group-hover:text-primary transition-colors cursor-pointer" onClick={() => navigateTo("Drivers", driver.id)}>
                {driver.name}
              </CardTitle>
              <p className="text-[10px] font-mono font-bold text-slate-400 tracking-widest uppercase">{driver.id}</p>
            </CardHeader>
            <CardContent className="space-y-4 pt-2">
              <div className="flex flex-col gap-3">
                 <div className="flex items-center justify-between text-xs font-medium text-slate-600">
                    <span className="text-[10px] uppercase font-bold text-slate-400">Exp. Seniority</span>
                    <span className="font-bold">{driver.yearsOfExperience || 10} Years</span>
                 </div>
                 <div className="flex flex-col gap-1">
                    <span className="text-[10px] uppercase font-bold text-slate-400">License Authority</span>
                    <span className="text-xs font-mono font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded border border-slate-100 italic">{driver.licenseNumber || "CERT-990011"}</span>
                 </div>
              </div>
              <div className="pt-2 flex flex-col gap-2">
                <span className="text-[9px] uppercase font-bold text-slate-400">Quick State Management</span>
                <div className="grid grid-cols-3 gap-1">
                  {(["Available", "Busy", "Off"] as const).map((st) => (
                    <Button 
                      key={st}
                      variant={driver.status === st ? "default" : "outline"} 
                      size="sm"
                      className={`h-6 px-0 text-[8px] font-black uppercase tracking-tighter ${
                        driver.status === st ? "" :
                        st === "Available" ? "text-emerald-600 border-emerald-100 hover:bg-emerald-50" :
                        st === "Busy" ? "text-blue-600 border-blue-100 hover:bg-blue-50" :
                        "text-slate-500 border-slate-100 hover:bg-slate-50"
                      }`}
                      onClick={async () => {
                        try {
                          await updateDoc(doc(db, "drivers", driver.id), { status: st });
                        } catch (e) {
                          handleFirestoreError(e, OperationType.UPDATE, `drivers/${driver.id}`);
                        }
                      }}
                    >
                      {st}
                    </Button>
                  ))}
                </div>
              </div>
              <div className="pt-2 border-t border-slate-50">
                 <Button 
                   variant="ghost" 
                   size="sm" 
                   className="w-full h-8 text-[10px] font-black uppercase tracking-tighter text-primary hover:bg-primary/5"
                   onClick={() => navigateTo("Drivers", driver.id)}
                 >
                    Access Personal File <ChevronRight size={14} className="ml-1" />
                 </Button>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}

function OrdersList({ orders, navigateTo, vehicles, clients, drivers, appUser }: { 
  orders: Order[], 
  navigateTo: any,
  vehicles: Vehicle[],
  clients: CorporateClient[],
  drivers: Driver[],
  appUser: AppUser
}) {
  const [activeTab, setActiveTab] = useState<string>("All");
  const [selectedSalesFilter, setSelectedSalesFilter] = useState<string>("All");
  const [sortKey, setSortKey] = useState<string>("id");
  const [sortOrder, setSortOrder] = useState<"asc" | "desc">("desc");

  const filteredOrders = useMemo(() => {
    let result = activeTab === "All" ? orders : orders.filter(o => o.status === activeTab);
    
    if (appUser.role !== "Sales" && selectedSalesFilter !== "All") {
      result = result.filter(o => o.salesId === selectedSalesFilter);
    }
    
    return [...result].sort((a, b) => {
      let aValue: any = a[sortKey as keyof Order] || "";
      let bValue: any = b[sortKey as keyof Order] || "";
      
      if (sortKey === "clientName") {
        aValue = clients.find(c => c.id === a.clientId)?.name || a.clientId;
        bValue = clients.find(c => c.id === b.clientId)?.name || b.clientId;
      }

      if (aValue < bValue) return sortOrder === "asc" ? -1 : 1;
      if (aValue > bValue) return sortOrder === "asc" ? 1 : -1;
      return 0;
    });
  }, [orders, activeTab, sortKey, sortOrder, clients]);

  const toggleSort = (key: string) => {
    if (sortKey === key) {
      setSortOrder(sortOrder === "asc" ? "desc" : "asc");
    } else {
      setSortKey(key);
      setSortOrder("asc");
    }
  };

  const SortIcon = ({ column }: { column: string }) => {
    if (sortKey !== column) return <ArrowUpDown size={10} className="ml-1 opacity-20" />;
    return sortOrder === "asc" ? <ArrowUp size={10} className="ml-1 text-primary" /> : <ArrowDown size={10} className="ml-1 text-primary" />;
  };

  return (
    <div className="space-y-4">
      <div className="flex flex-col md:flex-row gap-4 items-center justify-between">
        <div className="flex items-center gap-3 w-full md:w-auto">
          <Tabs defaultValue="All" value={activeTab} onValueChange={setActiveTab} className="w-fit">
            <TabsList className="bg-white border p-1 rounded-xl">
              <TabsTrigger value="All" className="text-xs font-bold uppercase tracking-tight data-[state=active]:bg-slate-100">All</TabsTrigger>
              <TabsTrigger value="Active" className="text-xs font-bold uppercase tracking-tight data-[state=active]:bg-emerald-50 data-[state=active]:text-emerald-700">Active</TabsTrigger>
              <TabsTrigger value="Completed" className="text-xs font-bold uppercase tracking-tight data-[state=active]:bg-blue-50 data-[state=active]:text-blue-700">Completed</TabsTrigger>
              <TabsTrigger value="Draft" className="text-xs font-bold uppercase tracking-tight data-[state=active]:bg-slate-100 data-[state=active]:text-slate-700">Draft</TabsTrigger>
            </TabsList>
          </Tabs>

          {appUser.role !== "Sales" && (
            <div className="w-48">
              <Select value={selectedSalesFilter} onValueChange={setSelectedSalesFilter}>
                <SelectTrigger className="h-9 bg-white border border-slate-200 rounded-xl text-[10px] font-bold uppercase tracking-tight">
                  <SelectValue placeholder="All Sales Executives" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="All" className="text-[10px]">All Sales Executives</SelectItem>
                  {MOCK_SALES_PEOPLE.map(s => (
                    <SelectItem key={s.id} value={s.id} className="text-[10px]">{s.name}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          )}
        </div>
        <CreateOrderDialog vehicles={vehicles} clients={clients} drivers={drivers} onCreate={() => {}} appUser={appUser} />
      </div>

      <Card className="border border-slate-200 rounded-xl shadow-sm overflow-hidden bg-white">
        <CardContent className="p-0">
          <div className="overflow-x-auto">
            <Table>
              <TableHeader className="bg-slate-50/50 border-b">
                <TableRow className="hover:bg-transparent border-none">
                  <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors" onClick={() => toggleSort("id")}>
                    <div className="flex items-center">Order ID <SortIcon column="id" /></div>
                  </TableHead>
                  <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors" onClick={() => toggleSort("clientName")}>
                    <div className="flex items-center">Client Partner <SortIcon column="clientName" /></div>
                  </TableHead>
                  <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors" onClick={() => toggleSort("vehicleId")}>
                    <div className="flex items-center">Asset Plate <SortIcon column="vehicleId" /></div>
                  </TableHead>
                  <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors" onClick={() => toggleSort("pickupDate")}>
                    <div className="flex items-center">Service window <SortIcon column="pickupDate" /></div>
                  </TableHead>
                  <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors" onClick={() => toggleSort("salesName")}>
                    <div className="flex items-center">Sales Executive <SortIcon column="salesName" /></div>
                  </TableHead>
                  <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors" onClick={() => toggleSort("price")}>
                    <div className="flex items-center">Net Value <SortIcon column="price" /></div>
                  </TableHead>
                  <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors" onClick={() => toggleSort("status")}>
                    <div className="flex items-center">State <SortIcon column="status" /></div>
                  </TableHead>
                </TableRow>
              </TableHeader>
              <TableBody className="divide-y divide-slate-100">
                {filteredOrders.map((order) => (
                  <TableRow key={order.id} className="hover:bg-slate-50 border-none transition-colors">
                    <TableCell className="px-4 py-3">
                      <div className="flex flex-col gap-1">
                        <button onClick={() => navigateTo("Sales", order.id)} className="font-mono text-xs font-bold text-primary hover:underline w-fit">
                          {order.id}
                        </button>
                        <Badge variant="outline" className="text-[8px] font-black uppercase tracking-tighter px-1 py-0 border-primary/20 text-primary w-fit">{order.businessLine || "Bluebird"}</Badge>
                      </div>
                    </TableCell>
                    <TableCell className="px-4 py-3 text-xs font-semibold text-primary hover:underline cursor-pointer" onClick={() => navigateTo("Clients", order.clientId)}>
                      {clients.find(c => c.id === order.clientId)?.name || order.clientId}
                    </TableCell>
                    <TableCell className="px-4 py-3 text-xs font-mono font-bold">
                      <span className="bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">{order.vehicleId}</span>
                    </TableCell>
                    <TableCell className="px-4 py-3 text-[10px] font-medium text-slate-600">
                      <div className="flex flex-col">
                        <span>{order.pickupDate}</span>
                        <span className="text-slate-400">— {order.returnDate}</span>
                      </div>
                    </TableCell>
                    <TableCell className="px-4 py-3 text-xs font-black text-slate-900">Rp {order.price.toLocaleString()}</TableCell>
                    <TableCell className="px-4 py-3 text-[10px] font-bold text-slate-500 italic">
                      {order.salesName || "Operations"}
                    </TableCell>
                    <TableCell className="px-4 py-3">
                      <Badge variant="outline" className={`text-[10px] font-bold rounded-full border-primary/20 uppercase tracking-tighter ${
                        order.status === 'Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 
                        order.status === 'Completed' ? 'bg-blue-50 text-blue-700 border-blue-100' : 
                        'bg-slate-50 text-slate-600 border-slate-100'
                      }`}>
                        {order.status}
                      </Badge>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}

function FinanceList({ orders, clients, navigateTo, partners }: { orders: Order[], clients: CorporateClient[], navigateTo: any, partners: Partner[] }) {
  const [activeTab, setActiveTab] = useState<string>("All");
  const [sortKey, setSortKey] = useState<string>("id");
  const [sortOrder, setSortOrder] = useState<"asc" | "desc">("desc");

  const toggleSort = (key: string) => {
    if (sortKey === key) {
      setSortOrder(sortOrder === "asc" ? "desc" : "asc");
    } else {
      setSortKey(key);
      setSortOrder("asc");
    }
  };

  const filteredOrders = useMemo(() => {
    let result = activeTab === "All" ? orders : orders.filter(o => o.paymentStatus === activeTab);
    
    return [...result].sort((a, b) => {
      let aValue: any = a[sortKey as keyof Order] || "";
      let bValue: any = b[sortKey as keyof Order] || "";
      
      // Map keys to readable values for sorting if needed
      if (sortKey === "clientId") {
        const clientA = clients.find(c => c.id === a.clientId)?.name || a.clientId;
        const clientB = clients.find(c => c.id === b.clientId)?.name || b.clientId;
        aValue = clientA;
        bValue = clientB;
      }

      if (typeof aValue === 'string') aValue = aValue.toLowerCase();
      if (typeof bValue === 'string') bValue = bValue.toLowerCase();

      if (aValue < bValue) return sortOrder === "asc" ? -1 : 1;
      if (aValue > bValue) return sortOrder === "asc" ? 1 : -1;
      return 0;
    });
  }, [orders, activeTab, sortKey, sortOrder, clients]);

  const SortIcon = ({ column }: { column: string }) => {
    if (sortKey !== column) return <ArrowUpDown size={12} className="ml-1 text-slate-300" />;
    return sortOrder === "asc" ? <ArrowUp size={12} className="ml-1 text-primary" /> : <ArrowDown size={12} className="ml-1 text-primary" />;
  };

  const generatePDF = (order: Order) => {
    const client = clients.find(c => c.id === order.clientId);
    const doc = new jsPDF();
    
    // Header
    doc.setFontSize(22);
    doc.setTextColor(0, 51, 153); // Bluebird Blue
    doc.text("BLUEBIRD B2B HUB", 14, 22);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text("Operational Invoice - Corporate Partner Account", 14, 30);
    doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 35);
    
    // Invoice Info Box
    doc.setDrawColor(200);
    doc.setFillColor(245, 245, 245);
    doc.rect(14, 45, 180, 25, "F");
    
    doc.setFontSize(11);
    doc.setTextColor(0);
    doc.setFont("helvetica", "bold");
    doc.text("Bill To:", 20, 52);
    doc.setFont("helvetica", "normal");
    doc.text(`${client?.name || order.clientId}`, 20, 58);
    doc.text(`${client?.address || "Registered Corporate Address"}`, 20, 64);
    
    doc.setFont("helvetica", "bold");
    doc.text("Invoice #:", 140, 52);
    doc.setFont("helvetica", "normal");
    doc.text(`INV-${order.id.split('-')[1]}`, 140, 58);
    
    // Table
    autoTable(doc, {
      startY: 80,
      head: [['Description', 'Asset ID', 'Period', 'Subtotal']],
      body: [
        [
          'Corporate Fleet Service - Dedicated Logistics',
          order.vehicleId,
          `${order.pickupDate} to ${order.returnDate}`,
          `Rp ${order.price.toLocaleString()}`
        ]
      ],
      headStyles: { fillColor: [0, 51, 153] },
      margin: { top: 80 }
    });
    
    const finalY = (doc as any).lastAutoTable.finalY + 10;
    
    doc.setFontSize(12);
    doc.setFont("helvetica", "bold");
    doc.text(`TOTAL AMOUNT: Rp ${order.price.toLocaleString()}`, 130, finalY + 10);
    
    doc.setFontSize(9);
    doc.setFont("helvetica", "italic");
    doc.setTextColor(150);
    doc.text("Thank you for your partnership with Bluebird Group.", 14, finalY + 30);
    doc.text("This is a computer generated document, no signature required.", 14, finalY + 35);
    
    doc.save(`Invoice_${order.id}.pdf`);
  };

  return (
    <div className="space-y-4">
      <div className="flex flex-col md:flex-row gap-4 items-center justify-between">
        <Tabs defaultValue="All" value={activeTab} onValueChange={setActiveTab} className="w-full md:w-auto">
          <TabsList className="bg-white border p-1 rounded-xl">
            <TabsTrigger value="All" className="text-xs font-bold uppercase tracking-tight data-[state=active]:bg-slate-100">All Statements</TabsTrigger>
            <TabsTrigger value="Paid" className="text-xs font-bold uppercase tracking-tight data-[state=active]:bg-emerald-50 data-[state=active]:text-emerald-700">Paid</TabsTrigger>
            <TabsTrigger value="Outstanding" className="text-xs font-bold uppercase tracking-tight data-[state=active]:bg-amber-50 data-[state=active]:text-amber-700">Outstanding</TabsTrigger>
          </TabsList>
        </Tabs>
        <div className="flex gap-2">
          <Button variant="outline" size="sm" className="h-8 text-xs font-bold border-slate-200">Reconcile All</Button>
          <Button size="sm" className="h-8 text-xs font-bold">New Invoice</Button>
        </div>
      </div>

      <Card className="border border-slate-200 rounded-xl shadow-sm overflow-hidden bg-white">
        <Table>
          <TableHeader className="bg-slate-50 border-b">
            <TableRow className="hover:bg-transparent border-none">
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("id")}
              >
                <div className="flex items-center">Invoice Ref # <SortIcon column="id" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("clientId")}
              >
                <div className="flex items-center">Partner Entity <SortIcon column="clientId" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("price")}
              >
                <div className="flex items-center">Statement Amount <SortIcon column="price" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("paymentStatus")}
              >
                <div className="flex items-center">Settlement <SortIcon column="paymentStatus" /></div>
              </TableHead>
              <TableHead 
                className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 cursor-pointer hover:text-primary transition-colors"
                onClick={() => toggleSort("returnDate")}
              >
                <div className="flex items-center">Term Date <SortIcon column="returnDate" /></div>
              </TableHead>
              <TableHead className="px-4 py-3 text-[10px] uppercase font-bold text-slate-500 text-right">Operations</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody className="divide-y divide-slate-100">
            {filteredOrders.map((order) => {
              const client = clients.find(c => c.id === order.clientId);
              
              const handleSettle = async () => {
                try {
                  await updateDoc(doc(db, "orders", order.id), { paymentStatus: "Paid" });
                } catch (e) {
                  handleFirestoreError(e, OperationType.UPDATE, `orders/${order.id}`);
                }
              };

              return (
                <TableRow key={order.id} className="hover:bg-slate-50 border-none transition-colors">
                  <TableCell className="px-4 py-3 font-mono text-xs font-bold text-slate-600 tracking-tighter">INV-{order.id.split('-')[1]}</TableCell>
                  <TableCell className="px-4 py-3 text-xs font-semibold text-primary hover:underline cursor-pointer" onClick={() => navigateTo("Clients", order.clientId)}>
                    {client?.name || order.clientId}
                  </TableCell>
                  <TableCell className="px-4 py-3 text-xs font-black text-slate-900">Rp {order.price.toLocaleString()}</TableCell>
                  <TableCell className="px-4 py-3">
                    <div className="flex items-center gap-2">
                      {order.paymentStatus === 'Paid' ? <CheckCircle2 size={12} className="text-emerald-500" /> : <Clock size={12} className="text-amber-500" />}
                      <span className={`text-[10px] font-bold uppercase tracking-tight ${order.paymentStatus === 'Paid' ? 'text-emerald-600' : 'text-amber-600'}`}>
                        {order.paymentStatus}
                      </span>
                    </div>
                  </TableCell>
                  <TableCell className="px-4 py-3 text-[10px] font-mono text-slate-500 font-medium">{order.returnDate}</TableCell>
                  <TableCell className="px-4 py-3 text-right">
                    <div className="flex justify-end gap-2">
                      {order.paymentStatus === "Outstanding" && (
                        <Button 
                          variant="outline" 
                          size="sm" 
                          className="h-7 px-2 text-[10px] font-black text-emerald-600 border-emerald-100 bg-emerald-50 hover:bg-emerald-100 hover:border-emerald-200"
                          onClick={handleSettle}
                        >
                          Mark as Paid
                        </Button>
                      )}
                      <Button 
                        variant="outline" 
                        size="sm" 
                        className="h-7 px-2 text-[10px] font-black border-slate-200 hover:bg-slate-50"
                        onClick={() => generatePDF(order)}
                      >
                        PDF
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              );
            })}
          </TableBody>
        </Table>
      </Card>
    </div>
  );
}
