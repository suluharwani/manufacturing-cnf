-- Date: 2021-06-29 09:00:00
CREATE INDEX idx_materials_id ON materials(id);
CREATE INDEX idx_materials_deleted_at ON materials(deleted_at);
CREATE INDEX idx_materials_detail_material_id ON materials_detail(material_id);
CREATE INDEX idx_type_id ON type(id);
CREATE INDEX idx_satuan_id ON satuan(id);



--fingerspot

-- Create an index on pegawai_status
CREATE INDEX idx_pegawai_status ON pegawai(pegawai_status);

-- Create indexes on columns used for searching
CREATE INDEX idx_pegawai_nip ON pegawai(pegawai_nip);
CREATE INDEX idx_pegawai_nama ON pegawai(pegawai_nama);
CREATE INDEX idx_pegawai_id ON pegawai(pegawai_id);

-- Create an index on pegawai_id for ordering
CREATE INDEX idx_pegawai_id_order ON pegawai(pegawai_id);
