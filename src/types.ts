export type EntityStatus = "Active" | "Inactive" | "Maintenance" | "Draft" | "Completed" | "Outstanding" | "Paid" | "Busy" | "Available";

export type BusinessLine = "Bluebird" | "Silverbird" | "Goldenbird" | "Big Bird" | "BirdMobil" | "Ironbird" | "Bluebird Kirim" | "Cititrans";

export interface CorporateClient {
  id: string;
  name: string;
  email: string;
  phone?: string;
  address: string;
  industry?: string;
  taxId?: string;
  status: EntityStatus;
  createdAt: Date;
}

export interface Vehicle {
  id: string;
  plateNumber: string;
  stnkNumber?: string;
  taxExpiryDate?: string;
  model: string;
  category: "Sedan" | "SUV" | "Bus" | "Luxury Sedan" | "Van";
  businessLine: BusinessLine;
  status: "Available" | "Maintenance" | "Busy";
  estimatedCompletionDate?: string;
  createdAt: Date;
}

export interface Driver {
  id: string;
  name: string;
  phone: string;
  licenseNumber?: string;
  licenseExpiry?: string;
  yearsOfExperience?: number;
  rating?: number;
  status: "Available" | "Busy" | "Off";
  assignedVehicleId?: string;
  createdAt: Date;
}

export interface Order {
  id: string;
  clientId: string;
  vehicleId: string;
  driverId: string;
  businessLine: BusinessLine;
  status: "Draft" | "Active" | "Completed";
  pickupDate: string;
  returnDate: string;
  price: number;
  paymentStatus: "Outstanding" | "Paid";
  salesId?: string;
  salesName?: string;
  createdAt: Date;
}

export interface Partner {
  id: string;
  name: string;
  type: "Maintenance" | "Insurance" | "Fuel" | "Sub-Contractor";
  contactPerson: string;
  phone: string;
  email: string;
  status: "Active" | "Inactive";
}

export interface MaintenanceAlert {
  id: string;
  vehicleId: string;
  alertType: "Near" | "Past";
  dueDate: string;
}
